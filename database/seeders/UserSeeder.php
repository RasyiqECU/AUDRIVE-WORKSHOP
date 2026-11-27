<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/** Akun demo. Semua password: "password". */
class UserSeeder extends Seeder
{
    /** @var list<array{0: string, 1: string, 2: string}> email, nama, role */
    private const STAFF = [
        ['superadmin@gmail.com', 'Super Admin', 'super-admin'],
        ['manager@gmail.com', 'Budi Santoso', 'manager'],
        ['owner@gmail.com', 'Hendra Gunawan', 'owner'],
        ['mekanik1@gmail.com', 'Joko Susilo', 'mechanic'],
        ['mekanik2@gmail.com', 'Agus Pratama', 'mechanic'],
        ['sales@gmail.com', 'Andi Wijaya', 'sales'],
        ['kasir@gmail.com', 'Siti Rahayu', 'cashier'],
        ['inventory@gmail.com', 'Rudi Hermawan', 'inventory'],
    ];

    /** email => [nama, kota, alamat, kendaraan[plat, merek, model, tahun, warna]] */
    private const CUSTOMERS = [
        'customer1@gmail.com' => ['Dewi Lestari', 'Jakarta', 'Jl. Sudirman No. 12', [
            ['B 1101 DLS', 'Porsche', '911 Carrera', 2022, 'Putih'],
            ['B 1102 DLS', 'BMW', 'X5 M', 2021, 'Hitam'],
        ]],
        'customer2@gmail.com' => ['Rizky Ramadhan', 'Bandung', 'Jl. Dago No. 45', [
            ['D 2201 RZR', 'Lamborghini', 'Huracán EVO', 2021, 'Kuning'],
        ]],
        'customer3@gmail.com' => ['Maria Angelina', 'Surabaya', 'Jl. Darmo No. 8', [
            ['L 3301 MRA', 'Mercedes-Benz', 'AMG C63', 2020, 'Silver'],
            ['L 3302 MRA', 'Audi', 'RS5', 2023, 'Abu-abu'],
        ]],
        'customer4@gmail.com' => ['Kevin Hartono', 'Jakarta', 'Jl. Kemang Raya No. 21', [
            ['B 4401 KVH', 'Ferrari', 'Roma', 2022, 'Merah'],
        ]],
        'customer5@gmail.com' => ['Nadia Putri', 'Tangerang', 'Jl. BSD Boulevard No. 3', [
            ['B 5501 NDP', 'McLaren', '570S', 2019, 'Oranye'],
        ]],
    ];

    public function run(): void
    {
        $phone = 81210000000;

        foreach (self::STAFF as [$email, $name, $role]) {
            $this->user($email, $name, $role, '0'.++$phone);
        }

        $number = 0;
        foreach (self::CUSTOMERS as $email => [$name, $city, $address, $vehicles]) {
            $customer = Customer::create([
                'user_id' => $this->user($email, $name, 'customer', '0'.++$phone)->id,
                'code' => 'CUS-'.str_pad((string) ++$number, 5, '0', STR_PAD_LEFT),
                'address' => $address,
                'city' => $city,
                'province' => $city === 'Jakarta' ? 'DKI Jakarta' : ($city === 'Surabaya' ? 'Jawa Timur' : ($city === 'Bandung' ? 'Jawa Barat' : 'Banten')),
            ]);

            foreach ($vehicles as [$plate, $brand, $model, $year, $color]) {
                $customer->vehicles()->create([
                    'plate_number' => $plate,
                    'brand' => $brand,
                    'model' => $model,
                    'year' => $year,
                    'color' => $color,
                ]);
            }
        }
    }

    private function user(string $email, string $name, string $role, string $phone): User
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make('password'),
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        return $user->assignRole($role);
    }
}
