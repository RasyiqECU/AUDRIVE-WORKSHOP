<?php


declare(strict_types=1);


namespace App\Services;


use App\Enums\SparePartStatus;
use App\Enums\StockMovementType;
use App\Models\SparePart;
use App\Models\SparePartCategory;
use App\Models\StockMovement;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;


class SparePartService
{
    public function getPublicCatalog(array $filters = []): LengthAwarePaginator
    {
        $query = SparePart::query()
            ->where('status', SparePartStatus::Active)
            ->with('category');


        // Filter by category
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }


        // Filter by brand compatibility
        if (!empty($filters['vehicle_brand'])) {
            $query->whereHas('compatibilities', function ($q) use ($filters) {
                $q->where('vehicle_brand', $filters['vehicle_brand']);
                if (!empty($filters['vehicle_model'])) {
                    $q->where('vehicle_model', $filters['vehicle_model']);
                }
                if (!empty($filters['vehicle_year'])) {
                    $q->where('year_from', '<=', $filters['vehicle_year'])
                      ->where('year_to', '>=', $filters['vehicle_year']);
                }
            });
        }


        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }


        // Sort
        $sort = $filters['sort'] ?? 'name_asc';
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price_idr', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price_idr', 'desc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default:
                $query->orderBy('name', 'asc');
        }


        return $query->paginate(12);
    }


    public function create(array $data): SparePart
    {
        return DB::transaction(function () use ($data) {
            $part = SparePart::create($data);


            // Create compatibilities if provided
            if (!empty($data['compatibilities'])) {
                foreach ($data['compatibilities'] as $compat) {
                    $part->compatibilities()->create($compat);
                }
            }


            return $part;
        });
    }


    public function update(SparePart $part, array $data): SparePart
    {
        return DB::transaction(function () use ($part, $data) {
            $part->update($data);


            // Update compatibilities if provided
            if (isset($data['compatibilities'])) {
                $part->compatibilities()->delete();
                foreach ($data['compatibilities'] as $compat) {
                    $part->compatibilities()->create($compat);
                }
            }


            return $part;
        });
    }


    public function adjustStock(SparePart $part, int $qty, string $type, ?string $notes = null): void
    {
        DB::transaction(function () use ($part, $qty, $type, $notes) {
            // Lock the part
            $lockedPart = SparePart::where('id', $part->id)
                ->lockForUpdate()
                ->first();


            if ($type === StockMovementType::Out->value && $lockedPart->stock < $qty) {
                throw new \InvalidArgumentException('Stok tidak cukup.');
            }


            // Update stock
            if ($type === StockMovementType::In->value) {
                $lockedPart->increment('stock', $qty);
            } elseif ($type === StockMovementType::Out->value) {
                $lockedPart->decrement('stock', $qty);
            } elseif ($type === StockMovementType::Adjustment->value) {
                $lockedPart->update(['stock' => $qty]);
            }


            // Create stock movement
            StockMovement::create([
                'spare_part_id' => $part->id,
                'type' => $type,
                'qty' => $type === StockMovementType::Adjustment->value ? abs($qty - $lockedPart->stock) : $qty,
                'reference_type' => null,
                'reference_id' => null,
                'notes' => $notes ?? 'Manual stock adjustment',
                'created_by' => auth()->id(),
            ]);
        });
    }


    public function getLowStockParts(): \Illuminate\Support\Collection
    {
        return SparePart::query()
            ->where('status', SparePartStatus::Active)
            ->whereColumn('stock', '<=', 'low_stock_threshold')
            ->with('category')
            ->orderBy('stock', 'asc')
            ->get();
    }
}



