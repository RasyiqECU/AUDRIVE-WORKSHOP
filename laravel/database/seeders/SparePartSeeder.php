<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SparePartStatus;
use App\Models\SparePart;
use App\Models\SparePartCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SparePartSeeder extends Seeder
{
    public function run(): void
    {
        $categories = SparePartCategory::pluck('id', 'name')->toArray();

        $parts = [
            [
                'sku' => 'AUD-IGN-002',
                'name' => 'Busi Iridium Racing NGK Laser Spark Plug',
                'brand' => 'NGK',
                'category_id' => $categories['Kelistrikan & Pengapian'] ?? 3,
                'price_idr' => 380000,
                'stock' => 32,
                'low_stock_threshold' => 8,
                'status' => SparePartStatus::Active,
                'description' => 'Busi elektroda iridium murni dengan ketahanan suhu ekstrem untuk stabilitas pengapian mesin high performance.',
            ],
            [
                'sku' => 'AUD-OIL-300',
                'name' => 'Oli Mesin Motul 300V Trophy 0W-40 (2L)',
                'brand' => 'Motul',
                'category_id' => $categories['Filter & Pelumas'] ?? 8,
                'price_idr' => 850000,
                'stock' => 45,
                'low_stock_threshold' => 10,
                'status' => SparePartStatus::Active,
                'description' => '100% synthetic racing lubricant dengan teknologi ESTER Core® untuk proteksi maksimal mesin performa tinggi.',
            ],
            [
                'sku' => 'AUD-SUS-501',
                'name' => 'Coilover Kit KW Suspension Variant 3',
                'brand' => 'KW Suspensions',
                'category_id' => $categories['Suspensi & Kaki-Kaki'] ?? 1,
                'price_idr' => 42000000,
                'stock' => 5,
                'low_stock_threshold' => 2,
                'status' => SparePartStatus::Active,
                'description' => 'Sistem suspensi balap jalan raya dengan pengaturan bump and rebound damping independen.',
            ],
            [
                'sku' => 'AUD-EXH-770',
                'name' => 'Titanium Slip-On Exhaust Akrapovič Evolution',
                'brand' => 'Akrapovic',
                'category_id' => $categories['Mesin & Performa'] ?? 4,
                'price_idr' => 78000000,
                'stock' => 3,
                'low_stock_threshold' => 1,
                'status' => SparePartStatus::Active,
                'description' => 'Knalpot berbahan titanium ultralight yang menghasilkan raungan suara khas supercar dan efisiensi aliran gas buang.',
            ],
            [
                'sku' => 'AUD-INT-102',
                'name' => 'Eventuri Carbon Fiber Cold Air Intake System',
                'brand' => 'Eventuri',
                'category_id' => $categories['Mesin & Performa'] ?? 4,
                'price_idr' => 28500000,
                'stock' => 6,
                'low_stock_threshold' => 2,
                'status' => SparePartStatus::Active,
                'description' => 'Rumah filter udara full carbon dengan prinsip venturi aerodinamis untuk pasokan udara dingin optimal.',
            ],
            [
                'sku' => 'AUD-BRK-990',
                'name' => 'Brembo Carbon Ceramic Brake Rotor 390mm',
                'brand' => 'Brembo',
                'category_id' => $categories['Sistem Pengereman'] ?? 2,
                'price_idr' => 35000000,
                'stock' => 4,
                'low_stock_threshold' => 2,
                'status' => SparePartStatus::Active,
                'description' => 'Piringan cakram carbon ceramic tahan suhu hingga 1000°C dengan bobot 50% lebih ringan dibanding baja konvensional.',
            ],
        ];

        foreach ($parts as $partData) {
            $slug = Str::slug($partData['name']);
            if (SparePart::where('sku', $partData['sku'])->exists()) {
                continue;
            }
            SparePart::create(array_merge($partData, ['slug' => $slug]));
        }
    }
}
