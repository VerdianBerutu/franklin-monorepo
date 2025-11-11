<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole('admin');

        // Create staf gudang user
        $stafGudang = User::create([
            'name' => 'Staf Gudang',
            'email' => 'staf@example.com',
            'password' => Hash::make('password123'),
        ]);
        $stafGudang->assignRole('staf_gudang');

        // Create manajer user
        $manajer = User::create([
            'name' => 'Manajer',
            'email' => 'manajer@example.com',
            'password' => Hash::make('password123'),
        ]);
        $manajer->assignRole('manajer');
    }
}