<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions (skip if exists)
        $permissions = [
            'view uploads', 'create uploads', 'edit uploads', 'delete uploads',
            'view sales', 'create sales', 'edit sales', 'delete sales', 'export sales',
            'view certificates', 'create certificates', 'edit certificates', 'delete certificates',
            'view users', 'create users', 'edit users', 'delete users',
            'view products', 'create products', 'edit products', 'delete products',  // ✅ ADD THIS
            'view customers', 'create customers', 'edit customers', 'delete customers', // ✅ ADD THIS
        ];

        foreach ($permissions as $permission) {
            // ✅ Use firstOrCreate to avoid duplicate error
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        // Create roles and assign permissions (skip if exists)
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all()); // Use sync instead of give

        $stafGudang = Role::firstOrCreate(['name' => 'staf_gudang']);
        $stafGudang->syncPermissions([
            'view uploads', 'create uploads', 'delete uploads',
            'view sales', 'create sales',
            'view certificates', 'create certificates',
            'view products', 'view customers', // ✅ ADD THIS
        ]);

        $manajer = Role::firstOrCreate(['name' => 'manajer']);
        $manajer->syncPermissions([
            'view uploads', 
            'view sales', 'export sales', 
            'view certificates',
            'view products', 'view customers', // ✅ ADD THIS
        ]);

        $this->command->info('Roles and permissions seeded successfully!');
    }
}