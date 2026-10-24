<?php


declare(strict_types=1);


namespace App\Services;


use App\Enums\StockMovementType;
use App\Models\SparePart;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;


class StockService
{
    public function stockIn(SparePart $part, int $qty, ?string $referenceType = null, ?int $referenceId = null, ?string $notes = null): StockMovement
    {
        return DB::transaction(function () use ($part, $qty, $referenceType, $referenceId, $notes) {
            // Lock the part
            $lockedPart = SparePart::where('id', $part->id)
                ->lockForUpdate()
                ->first();


            // Increment stock
            $lockedPart->increment('stock', $qty);


            // Create stock movement
            return StockMovement::create([
                'spare_part_id' => $part->id,
                'type' => StockMovementType::In,
                'qty' => $qty,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes ?? 'Stock in',
                'created_by' => auth()->id(),
            ]);
        });
    }


    public function stockOut(SparePart $part, int $qty, ?string $referenceType = null, ?int $referenceId = null, ?string $notes = null): StockMovement
    {
        return DB::transaction(function () use ($part, $qty, $referenceType, $referenceId, $notes) {
            // Lock the part
            $lockedPart = SparePart::where('id', $part->id)
                ->lockForUpdate()
                ->first();


            // Check stock availability
            if ($lockedPart->stock < $qty) {
                throw new \Exception("Stok tidak cukup untuk {$part->name}. Tersedia: {$lockedPart->stock}, Diminta: {$qty}");
            }


            // Decrement stock
            $lockedPart->decrement('stock', $qty);


            // Create stock movement
            return StockMovement::create([
                'spare_part_id' => $part->id,
                'type' => StockMovementType::Out,
                'qty' => $qty,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes ?? 'Stock out',
                'created_by' => auth()->id(),
            ]);
        });
    }


    public function adjustStock(SparePart $part, int $newStock, ?string $notes = null): StockMovement
    {
        return DB::transaction(function () use ($part, $newStock, $notes) {
            // Lock the part
            $lockedPart = SparePart::where('id', $part->id)
                ->lockForUpdate()
                ->first();


            $oldStock = $lockedPart->stock;
            $diff = $newStock - $oldStock;


            if ($diff === 0) {
                throw new \Exception("Stok sudah sama dengan {$newStock}.");
            }


            // Update stock
            $lockedPart->update(['stock' => $newStock]);


            // Create stock movement
            return StockMovement::create([
                'spare_part_id' => $part->id,
                'type' => StockMovementType::Adjustment,
                'qty' => abs($diff),
                'reference_type' => null,
                'reference_id' => null,
                'notes' => $notes ?? "Stock adjustment: {$oldStock} -> {$newStock}",
                'created_by' => auth()->id(),
            ]);
        });
    }


    public function getStockMovements(SparePart $part, int $limit = 50): \Illuminate\Support\Collection
    {
        return StockMovement::where('spare_part_id', $part->id)
            ->with('creator')
            ->latest()
            ->limit($limit)
            ->get();
    }


    public function getLowStockParts(int $limit = 20): \Illuminate\Support\Collection
    {
        return SparePart::whereColumn('stock', '<=', 'low_stock_threshold')
            ->with('category')
            ->orderByRaw('(low_stock_threshold - stock) DESC')
            ->limit($limit)
            ->get();
    }
}



