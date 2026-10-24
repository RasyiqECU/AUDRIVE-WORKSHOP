<?php


namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        if (!class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            return;
        }

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();


        // ── Create Permissions ──
        $permissions = [
            // Cars
            'cars.view',
            'cars.create',
            'cars.update',
            'cars.delete',
            'cars.inspect',


            // Offers
            'offers.view',
            'offers.create',
            'offers.update',
            'offers.delete',
            'offers.manage',


            // Appointments
            'appointments.view',
            'appointments.create',
            'appointments.update',
            'appointments.delete',
            'appointments.manage',


            // Test Drives
            'test-drives.view',
            'test-drives.create',
            'test-drives.manage',


            // Service Bookings
            'service-bookings.view',
            'service-bookings.create',
            'service-bookings.update',
            'service-bookings.delete',
            'service-bookings.manage',


            // Work Orders
            'work-orders.view',
            'work-orders.create',
            'work-orders.update',
            'work-orders.delete',
            'work-orders.manage',


            // AI Scans
            'ai-scans.view',
            'ai-scans.create',
            'ai-scans.review',


            // Spare Parts
            'spare-parts.view',
            'spare-parts.create',
            'spare-parts.update',
            'spare-parts.delete',
            'spare-parts.manage',


            // Inventory
            'inventory.view',
            'inventory.manage',


            // Purchasing
            'purchasing.view',
            'purchasing.create',
            'purchasing.update',
            'purchasing.manage',


            // POS
            'pos.use',


            // Finance
            'finance.view',
            'finance.manage',


            // Reports
            'reports.view',


            // Users
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.manage',


            // Settings
            'settings.view',
            'settings.manage',


            // Audit
            'audit.view',


            // Approvals
            'approvals.view',
            'approvals.create',
            'approvals.manage',


            // CRM
            'crm.view',
            'crm.manage',


            // Tickets
            'tickets.view',
            'tickets.create',
            'tickets.update',
            'tickets.manage',
        ];


        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }


        // ── Create Roles ──
        $roles = [
            'customer' => [
                'cars.view',
                'offers.view',
                'offers.create',
                'appointments.view',
                'appointments.create',
                'test-drives.create',
                'service-bookings.view',
                'service-bookings.create',
                'ai-scans.view',
                'ai-scans.create',
                'spare-parts.view',
                'tickets.view',
                'tickets.create',
            ],
            'mechanic' => [
                'cars.view',
                'work-orders.view',
                'work-orders.update',
                'ai-scans.view',
                'ai-scans.create',
                'ai-scans.review',
                'spare-parts.view',
                'service-bookings.view',
            ],
            'sales' => [
                'cars.view',
                'cars.create',
                'cars.update',
                'cars.inspect',
                'offers.view',
                'offers.manage',
                'appointments.view',
                'appointments.manage',
                'test-drives.view',
                'test-drives.create',
                'test-drives.manage',
                'spare-parts.view',
                'reports.view',
                'crm.view',
            ],
            'cashier' => [
                'cars.view',
                'offers.view',
                'appointments.view',
                'appointments.manage',
                'service-bookings.view',
                'service-bookings.manage',
                'work-orders.view',
                'spare-parts.view',
                'pos.use',
                'finance.view',
            ],
            'inventory' => [
                'spare-parts.view',
                'spare-parts.create',
                'spare-parts.update',
                'spare-parts.manage',
                'inventory.view',
                'inventory.manage',
                'purchasing.view',
                'purchasing.create',
                'purchasing.update',
                'purchasing.manage',
            ],
            'manager' => [
                'cars.view',
                'cars.create',
                'cars.update',
                'cars.delete',
                'cars.inspect',
                'offers.view',
                'offers.manage',
                'appointments.view',
                'appointments.manage',
                'test-drives.view',
                'test-drives.manage',
                'service-bookings.view',
                'service-bookings.manage',
                'work-orders.view',
                'work-orders.create',
                'work-orders.update',
                'work-orders.manage',
                'ai-scans.view',
                'ai-scans.review',
                'spare-parts.view',
                'spare-parts.create',
                'spare-parts.update',
                'spare-parts.manage',
                'inventory.view',
                'inventory.manage',
                'purchasing.view',
                'purchasing.create',
                'purchasing.update',
                'purchasing.manage',
                'pos.use',
                'finance.view',
                'finance.manage',
                'reports.view',
                'users.view',
                'settings.view',
                'audit.view',
                'approvals.view',
                'approvals.create',
                'approvals.manage',
                'crm.view',
                'crm.manage',
                'tickets.view',
                'tickets.manage',
            ],
            'super-admin' => [], // Super-admin gets all permissions via Gate::before
        ];


        foreach ($roles as $role => $rolePermissions) {
            $roleObj = Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);


            if ($role !== 'super-admin') {
                $roleObj->syncPermissions($rolePermissions);
            }
        }


        $this->command->info('Roles and permissions created successfully.');
    }
}



