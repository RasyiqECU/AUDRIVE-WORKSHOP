<?php


namespace Database\Seeders;


use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Super Admin ──
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@audrive.local',
            'phone' => '081234567890',
            'password' => Hash::make('password'),
            'role' => 'super-admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('super-admin');


        // ── Manager ──
        $manager = User::create([
            'name' => 'Budi Santoso',
            'email' => 'manager@audrive.local',
            'phone' => '081234567891',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $manager->assignRole('manager');


        // ── Sales ──
        $sales = User::create([
            'name' => 'Andi Wijaya',
            'email' => 'sales@audrive.local',
            'phone' => '081234567892',
            'password' => Hash::make('password'),
            'role' => 'sales',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $sales->assignRole('sales');


        // ── Mechanic ──
        $mechanic = User::create([
            'name' => 'Joko Susilo',
            'email' => 'mechanic@audrive.local',
            'phone' => '081234567893',
            'password' => Hash::make('password'),
            'role' => 'mechanic',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $mechanic->assignRole('mechanic');


        // ── Cashier ──
        $cashier = User::create([
            'name' => 'Siti Rahayu',
            'email' => 'cashier@audrive.local',
            'phone' => '081234567894',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $cashier->assignRole('cashier');


        // ── Inventory ──
        $inventory = User::create([
            'name' => 'Rudi Hermawan',
            'email' => 'inventory@audrive.local',
            'phone' => '081234567895',
            'password' => Hash::make('password'),
            'role' => 'inventory',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $inventory->assignRole('inventory');


        // ── Customer ──
        $customerUser = User::create([
            'name' => 'Dewi Lestari',
            'email' => 'customer@audrive.local',
            'phone' => '081234567896',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $customerUser->assignRole('customer');


        Customer::create([
            'user_id' => $customerUser->id,
            'code' => 'CUS-DEWI01',
            'address' => 'Jl. Sudirman No. 123',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'birth_date' => '1990-05-15',
            'loyalty_points' => 1500,
        ]);


        // ── Additional Customers ──
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => fake()->name(),
                'email' => "customer{$i}@audrive.local",
                'phone' => '08' . fake()->numerify('##########'),
                'password' => Hash::make('password'),
                'role' => 'customer',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            $user->assignRole('customer');


            Customer::create([
                'user_id' => $user->id,
                'code' => 'CUS-' . strtoupper(fake()->regexify('[A-Z0-9]{6}')),
                'address' => fake()->address(),
                'city' => fake()->randomElement(['Jakarta', 'Bandung', 'Surabaya']),
                'province' => 'DKI Jakarta',
                'birth_date' => fake()->dateTimeBetween('-50 years', '-20 years'),
                'loyalty_points' => fake()->numberBetween(0, 3000),
            ]);
        }


        $this->command->info('Demo users created successfully.');
    }
}



