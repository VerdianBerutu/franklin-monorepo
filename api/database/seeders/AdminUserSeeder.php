<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Create admin user (skip if exists)
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
            ]
        );
        
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // ✅ Create staf gudang user (skip if exists)
        $stafGudang = User::firstOrCreate(
            ['email' => 'staf@example.com'],
            [
                'name' => 'Staf Gudang',
                'password' => Hash::make('password123'),
            ]
        );
        
        if (!$stafGudang->hasRole('staf_gudang')) {
            $stafGudang->assignRole('staf_gudang');
        }

        // ✅ Create manajer user (skip if exists)
        $manajer = User::firstOrCreate(
            ['email' => 'manajer@example.com'],
            [
                'name' => 'Manajer',
                'password' => Hash::make('password123'),
            ]
        );
        
        if (!$manajer->hasRole('manajer')) {
            $manajer->assignRole('manajer');
        }

        $this->command->info('Users seeded successfully!');
    }
}