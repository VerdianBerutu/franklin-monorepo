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

        // Create permissions
        $permissions = [
            'view uploads', 'create uploads', 'edit uploads', 'delete uploads',
            'view sales', 'create sales', 'edit sales', 'delete sales', 'export sales',
            'view certificates', 'create certificates', 'edit certificates', 'delete certificates',
            'view users', 'create users', 'edit users', 'delete users',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $stafGudang = Role::create(['name' => 'staf_gudang']);
        $stafGudang->givePermissionTo([
            'view uploads', 'create uploads', 'delete uploads',
            'view sales', 'create sales',
            'view certificates', 'create certificates',
        ]);

        $manajer = Role::create(['name' => 'manajer']);
        $manajer->givePermissionTo([
            'view uploads', 
            'view sales', 'export sales', 
            'view certificates'
        ]);
    }
}