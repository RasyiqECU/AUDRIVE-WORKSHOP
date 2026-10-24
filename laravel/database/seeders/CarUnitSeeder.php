<?php


namespace Database\Seeders;


use App\Enums\CarUnitStatus;
use App\Models\CarPhoto;
use App\Models\CarUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class CarUnitSeeder extends Seeder
{
    public function run(): void
    {
        $cars = [
            [
                'brand' => 'Lamborghini',
                'model' => 'Huracán EVO',
                'year' => 2024,
                'transmission' => 'automatic',
                'color' => 'Giallo Orion',
                'mileage_km' => 5200,
                'price_buy_idr' => 4500000000,
                'price_sell_idr' => 5200000000,
                'status' => CarUnitStatus::Available,
                'description' => 'Lamborghini Huracán EVO dengan warna Giallo Orion yang ikonik. Kondisi sempurna, servis rutin di bengkel resmi.',
                'stock_status' => 'available',
                'warranty_status' => 'active',
                'stnk_status' => 'valid',
                'bpkb_status' => 'available',
                'location' => 'Showroom Jakarta',
                'fuel_type' => 'bensin',
                'engine_cc' => '5200',
                'featured_at' => now(),
            ],
            [
                'brand' => 'Ferrari',
                'model' => 'F8 Tributo',
                'year' => 2023,
                'transmission' => 'automatic',
                'color' => 'Rosso Corsa',
                'mileage_km' => 8500,
                'price_buy_idr' => 4000000000,
                'price_sell_idr' => 4800000000,
                'status' => CarUnitStatus::Available,
                'description' => 'Ferrari F8 Tributo dengan mesin V8 twin-turbo yang bertenaga. Interior kulit hitam premium.',
                'stock_status' => 'available',
                'warranty_status' => 'active',
                'stnk_status' => 'valid',
                'bpkb_status' => 'available',
                'location' => 'Showroom Jakarta',
                'fuel_type' => 'bensin',
                'engine_cc' => '3902',
                'featured_at' => now(),
            ],
            [
                'brand' => 'Porsche',
                'model' => '911 Turbo S',
                'year' => 2024,
                'transmission' => 'automatic',
                'color' => 'GT Silver',
                'mileage_km' => 3100,
                'price_buy_idr' => 3200000000,
                'price_sell_idr' => 3900000000,
                'status' => CarUnitStatus::Available,
                'description' => 'Porsche 911 Turbo S dengan performa luar biasa. Full option dengan Sport Chrono Package.',
                'stock_status' => 'available',
                'warranty_status' => 'active',
                'stnk_status' => 'valid',
                'bpkb_status' => 'available',
                'location' => 'Showroom Bandung',
                'fuel_type' => 'bensin',
                'engine_cc' => '3745',
                'featured_at' => now(),
            ],
            [
                'brand' => 'Lamborghini',
                'model' => 'Urus',
                'year' => 2023,
                'transmission' => 'automatic',
                'color' => 'Nero Noctis',
                'mileage_km' => 12000,
                'price_buy_idr' => 3800000000,
                'price_sell_idr' => 4500000000,
                'status' => CarUnitStatus::Reserved,
                'description' => 'Lamborghini Urus Super SUV dengan tenaga 650 HP. Cocok untuk penggunaan harian dan performa tinggi.',
                'stock_status' => 'reserved',
                'warranty_status' => 'active',
                'stnk_status' => 'valid',
                'bpkb_status' => 'available',
                'location' => 'Showroom Jakarta',
                'fuel_type' => 'bensin',
                'engine_cc' => '4000',
            ],
            [
                'brand' => 'McLaren',
                'model' => '720S',
                'year' => 2022,
                'transmission' => 'automatic',
                'color' => 'Papaya Spark',
                'mileage_km' => 15000,
                'price_buy_idr' => 3500000000,
                'price_sell_idr' => 4200000000,
                'status' => CarUnitStatus::Available,
                'description' => 'McLaren 720S dengan desain futuristik dan performa supercar sejati.',
                'stock_status' => 'available',
                'warranty_status' => 'expired',
                'stnk_status' => 'valid',
                'bpkb_status' => 'available',
                'location' => 'Showroom Jakarta',
                'fuel_type' => 'bensin',
                'engine_cc' => '4000',
            ],
            [
                'brand' => 'BMW',
                'model' => 'M8 Competition',
                'year' => 2023,
                'transmission' => 'automatic',
                'color' => 'Alvinit Grey',
                'mileage_km' => 18000,
                'price_buy_idr' => 2200000000,
                'price_sell_idr' => 2800000000,
                'status' => CarUnitStatus::Available,
                'description' => 'BMW M8 Competition gran coupe dengan kemewahan dan performa tinggi.',
                'stock_status' => 'available',
                'warranty_status' => 'active',
                'stnk_status' => 'valid',
                'bpkb_status' => 'available',
                'location' => 'Showroom Surabaya',
                'fuel_type' => 'bensin',
                'engine_cc' => '4400',
            ],
            [
                'brand' => 'Mercedes-Benz',
                'model' => 'AMG GT 63 S',
                'year' => 2023,
                'transmission' => 'automatic',
                'color' => 'Obsidian Black',
                'mileage_km' => 9500,
                'price_buy_idr' => 2800000000,
                'price_sell_idr' => 3500000000,
                'status' => CarUnitStatus::InInspection,
                'description' => 'Mercedes-AMG GT 63 S four-door coupe. Performa AMG dengan kenyamanan sedan.',
                'stock_status' => 'available',
                'warranty_status' => 'active',
                'stnk_status' => 'valid',
                'bpkb_status' => 'available',
                'location' => 'Showroom Jakarta',
                'fuel_type' => 'bensin',
                'engine_cc' => '4000',
            ],
            [
                'brand' => 'Audi',
                'model' => 'R8 V10 Plus',
                'year' => 2022,
                'transmission' => 'automatic',
                'color' => 'Suzuka Grey',
                'mileage_km' => 22000,
                'price_buy_idr' => 2500000000,
                'price_sell_idr' => 3200000000,
                'status' => CarUnitStatus::Sold,
                'description' => 'Audi R8 V10 Plus dengan mesin naturally-aspirated V10. Supercar harian terbaik.',
                'stock_status' => 'sold',
                'warranty_status' => 'expired',
                'stnk_status' => 'valid',
                'bpkb_status' => 'available',
                'location' => 'Showroom Jakarta',
                'fuel_type' => 'bensin',
                'engine_cc' => '5200',
            ],
            [
                'brand' => 'Porsche',
                'model' => 'Cayenne Turbo GT',
                'year' => 2024,
                'transmission' => 'automatic',
                'color' => 'Carmine Red',
                'mileage_km' => 2500,
                'price_buy_idr' => 2900000000,
                'price_sell_idr' => 3600000000,
                'status' => CarUnitStatus::Available,
                'description' => 'Porsche Cayenne Turbo GT, SUV tercepat di Nürburgring. Performa supercar dalam bentuk SUV.',
                'stock_status' => 'available',
                'warranty_status' => 'active',
                'stnk_status' => 'valid',
                'bpkb_status' => 'available',
                'location' => 'Showroom Bandung',
                'fuel_type' => 'bensin',
                'engine_cc' => '4000',
                'featured_at' => now(),
            ],
            [
                'brand' => 'Ferrari',
                'model' => 'Roma',
                'year' => 2023,
                'transmission' => 'automatic',
                'color' => 'Blu Tour de France',
                'mileage_km' => 6800,
                'price_buy_idr' => 3100000000,
                'price_sell_idr' => 3800000000,
                'status' => CarUnitStatus::Available,
                'description' => 'Ferrari Roma grand tourer yang elegan. Desain timeless dengan performa V8 turbo.',
                'stock_status' => 'available',
                'warranty_status' => 'active',
                'stnk_status' => 'valid',
                'bpkb_status' => 'available',
                'location' => 'Showroom Jakarta',
                'fuel_type' => 'bensin',
                'engine_cc' => '3855',
            ],
        ];


        foreach ($cars as $carData) {
            $slug = Str::slug("{$carData['year']} {$carData['brand']} {$carData['model']}");


            // Ensure unique slug
            $count = 1;
            $originalSlug = $slug;
            while (CarUnit::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }


            $car = CarUnit::create(array_merge($carData, [
                'code' => 'CAR-' . strtoupper(Str::random(6)),
                'slug' => $slug,
            ]));


            // Create sample photos
            for ($i = 0; $i < 4; $i++) {
                CarPhoto::create([
                    'car_unit_id' => $car->id,
                    'path' => 'cars/sample-' . ($i + 1) . '.jpg',
                    'is_primary' => $i === 0,
                    'sort_order' => $i,
                    'alt_text' => $car->full_name . ' - Photo ' . ($i + 1),
                ]);
            }
        }


        $this->command->info('Car units seeded successfully.');
    }
}



