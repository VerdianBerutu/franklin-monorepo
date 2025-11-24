<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $this->command->error('No user found! Please create a user first.');
            return;
        }

        $products = [
            [
                'name' => 'Laptop Dell XPS 13',
                'sku' => 'LAPTOP-001',
                'description' => 'High-performance laptop',
                'price' => 15000000,
                'cost' => 12000000,
                'stock' => 10,
                'unit' => 'pcs',
                'category' => 'Electronics',
                'is_active' => true,
                'user_id' => $user->id
            ],
            [
                'name' => 'Office Chair Premium',
                'sku' => 'CHAIR-001',
                'description' => 'Ergonomic office chair',
                'price' => 2500000,
                'cost' => 1800000,
                'stock' => 25,
                'unit' => 'pcs',
                'category' => 'Furniture',
                'is_active' => true,
                'user_id' => $user->id
            ],
            [
                'name' => 'Printer HP LaserJet',
                'sku' => 'PRINT-001',
                'description' => 'Laser printer',
                'price' => 3500000,
                'cost' => 2800000,
                'stock' => 15,
                'unit' => 'pcs',
                'category' => 'Electronics',
                'is_active' => true,
                'user_id' => $user->id
            ]
        ];

        foreach ($products as $productData) {
            // ✅ Use firstOrCreate to avoid duplicate
            Product::firstOrCreate(
                ['sku' => $productData['sku']], // Check by SKU
                $productData // Create with full data if not exists
            );
        }

        $this->command->info('Products seeded successfully!');
    }
}