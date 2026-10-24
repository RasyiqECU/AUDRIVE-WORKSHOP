<?php


declare(strict_types=1);


namespace App\Services;


use App\Enums\CarUnitStatus;
use App\Models\CarInspection;
use App\Models\CarPhoto;
use App\Models\CarUnit;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class CarUnitService
{
    public function __construct(
        protected ?AuditService $auditService = null
    ) {
        $this->auditService = $auditService ?? app(AuditService::class);
    }

    public function create(array $data): CarUnit
    {
        $data['code'] = $this->generateCode();
        $data['slug'] = $this->generateSlug($data);


        return CarUnit::create($data);
    }


    public function update(CarUnit $carUnit, array $data): CarUnit
    {
        $oldStatus = $carUnit->status;
        $oldPrice = $carUnit->price_sell_idr;


        if (isset($data['name'])) {
            $data['slug'] = $this->generateSlug($data);
        }


        $carUnit->update($data);


        // Audit log untuk perubahan status
        if ($oldStatus !== $carUnit->status) {
            $this->logStatusChange($carUnit, $oldStatus, $carUnit->status);
        }


        // Audit log untuk perubahan harga
        if ($oldPrice !== $carUnit->price_sell_idr) {
            $this->logPriceChange($carUnit, $oldPrice, $carUnit->price_sell_idr);
        }


        return $carUnit;
    }


    public function updateStatus(CarUnit $carUnit, CarUnitStatus $status): CarUnit
    {
        $oldStatus = $carUnit->status;
        $carUnit->update(['status' => $status]);


        $this->logStatusChange($carUnit, $oldStatus, $status);


        return $carUnit;
    }


    public function uploadPhotos(CarUnit $carUnit, array $photos, bool $setFirstAsPrimary = true): Collection
    {
        $uploadedPhotos = DB::transaction(function () use ($carUnit, $photos, $setFirstAsPrimary) {
            $result = collect();

            foreach ($photos as $index => $photo) {
                $path = $photo->store('cars/' . $carUnit->id, 'public');

                if ($path === false) {
                    throw new \RuntimeException("Gagal mengupload foto ke-{$index}.");
                }

                $carPhoto = CarPhoto::create([
                    'car_unit_id' => $carUnit->id,
                    'path'        => $path,
                    'is_primary'  => $setFirstAsPrimary && $index === 0,
                    'sort_order'  => $carUnit->photos()->count() + $index,
                    'alt_text'    => $carUnit->full_name,
                ]);

                $result->push($carPhoto);
            }

            return $result;
        });

        return $uploadedPhotos;
    }


    public function setPrimaryPhoto(CarPhoto $photo): void
    {
        DB::transaction(function () use ($photo) {
            // Reset semua primary
            CarPhoto::where('car_unit_id', $photo->car_unit_id)
                ->update(['is_primary' => false]);


            // Set foto ini sebagai primary
            $photo->update(['is_primary' => true]);
        });
    }


    public function reorderPhotos(CarUnit $carUnit, array $photoIds): void
    {
        DB::transaction(function () use ($carUnit, $photoIds) {
            foreach ($photoIds as $index => $photoId) {
                CarPhoto::where('id', $photoId)
                    ->where('car_unit_id', $carUnit->id)
                    ->update(['sort_order' => $index]);
            }
        });
    }


    public function deletePhoto(CarPhoto $photo): void
    {
        DB::transaction(function () use ($photo) {
            // Hapus file
            Storage::disk('public')->delete($photo->path);


            // Hapus record
            $photo->delete();


            // Jika ini primary, set foto lain sebagai primary
            if ($photo->is_primary) {
                $nextPhoto = CarPhoto::where('car_unit_id', $photo->car_unit_id)
                    ->orderBy('sort_order')
                    ->first();


                if ($nextPhoto) {
                    $nextPhoto->update(['is_primary' => true]);
                }
            }
        });
    }


    public function createInspection(CarUnit $carUnit, int $inspectorId, array $checklist, ?string $notes = null, ?int $aiScanId = null): CarInspection
    {
        $result = $this->calculateInspectionResult($checklist);


        return CarInspection::create([
            'car_unit_id' => $carUnit->id,
            'inspector_id' => $inspectorId,
            'checklist' => $checklist,
            'notes' => $notes,
            'ai_scan_id' => $aiScanId,
            'result' => $result,
            'inspected_at' => now(),
        ]);
    }


    public function markAsFeatured(CarUnit $carUnit): CarUnit
    {
        return $this->update($carUnit, ['featured_at' => now()]);
    }


    public function removeFeatured(CarUnit $carUnit): CarUnit
    {
        return $this->update($carUnit, ['featured_at' => null]);
    }


    protected function generateCode(): string
    {
        do {
            $code = 'CAR-' . strtoupper(Str::random(6));
        } while (CarUnit::where('code', $code)->exists());


        return $code;
    }


    protected function generateSlug(array $data): string
    {
        $base = Str::slug("{$data['year']} {$data['brand']} {$data['model']}");
        $slug = $base;
        $count = 1;


        while (CarUnit::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $count;
            $count++;
        }


        return $slug;
    }


    protected function calculateInspectionResult(array $checklist): string
    {
        $scores = array_values($checklist);
        $badCount = count(array_filter($scores, fn($s) => $s === 'buruk'));


        if ($badCount === 0) {
            return 'pass';
        } elseif ($badCount <= 2) {
            return 'conditional';
        } else {
            return 'fail';
        }
    }


    protected function logStatusChange(CarUnit $carUnit, CarUnitStatus $oldStatus, CarUnitStatus $newStatus): void
    {
        $this->auditService->log(
            action: 'status_changed',
            modelType: 'car_unit',
            modelId: $carUnit->id,
            oldValues: ['status' => $oldStatus->value],
            newValues: ['status' => $newStatus->value],
            user: auth()->user(),
            description: "Status unit mobil {$carUnit->full_name} diubah dari {$oldStatus->value} ke {$newStatus->value}"
        );
    }


    protected function logPriceChange(CarUnit $carUnit, int $oldPrice, int $newPrice): void
    {
        $this->auditService->logPriceChange(
            modelType: 'car_unit',
            modelId: $carUnit->id,
            oldPrice: $oldPrice,
            newPrice: $newPrice,
            user: auth()->user()
        );
    }
}



