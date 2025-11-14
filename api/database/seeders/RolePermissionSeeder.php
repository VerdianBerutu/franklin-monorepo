<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create Permissions
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Product Management
            'view products',
            'create products',
            'edit products',
            'delete products',

            // Customer Management
            'view customers',
            'create customers',
            'edit customers',
            'delete customers',

            // Sales Management
            'view sales',
            'create sales',
            'edit sales',
            'delete sales',

            // Certificate Management
            'view certificates',
            'create certificates',
            'edit certificates',
            'delete certificates',

            // Upload Management
            'view uploads',
            'create uploads',
            'edit uploads',
            'delete uploads',

            // Dashboard
            'view dashboard',

            // Reports
            'view reports',
            'export reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $viewerRole = Role::firstOrCreate(['name' => 'viewer']);
        $userRole = Role::firstOrCreate(['name' => 'user']); 

        // Assign Permissions to Roles

        // Admin - All permissions
        $adminRole->syncPermissions($permissions);

        // Staff - Can manage most things except user management
        $staffPermissions = [
            'view products', 'create products', 'edit products',
            'view customers', 'create customers', 'edit customers',
            'view sales', 'create sales', 'edit sales',
            'view certificates', 'create certificates', 'edit certificates',
            'view uploads', 'create uploads',
            'view dashboard',
            'view reports', 'export reports',
        ];
        $staffRole->syncPermissions($staffPermissions);

        // Viewer - Can only view
        $viewerPermissions = [
            'view products',
            'view customers',
            'view sales',
            'view certificates',
            'view uploads',
            'view dashboard',
            'view reports',
        ];
        $viewerRole->syncPermissions($viewerPermissions);

        // User - Same permissions as viewer (TAMBAHKAN INI)
        $userRole->syncPermissions($viewerPermissions);

        // Assign roles to existing admin user
        $adminUser = User::find(1);
        if ($adminUser) {
            $adminUser->assignRole('admin');
        }
    }
}