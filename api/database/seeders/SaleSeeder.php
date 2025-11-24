<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        
        if (!$user) {
            $this->command->error('No user found!');
            return;
        }

        $customers = Customer::all();
        $products = Product::all();

        if ($customers->count() === 0 || $products->count() === 0) {
            $this->command->error('No customers or products found! Run CustomerSeeder and ProductSeeder first.');
            return;
        }

        // ✅ Skip if sales already exist
        if (Sale::count() > 0) {
            $this->command->info('Sales already seeded. Skipping...');
            return;
        }

        // Sale 1
        $sale1 = Sale::create([
            'customer_id' => $customers[0]->id,
            'customer_name' => $customers[0]->name,
            'sale_date' => now()->subDays(5),
            'tax' => 100000,
            'discount' => 0,
            'payment_status' => 'paid',
            'payment_method' => 'bank_transfer',
            'payment_date' => now()->subDays(4),
            'notes' => 'First order',
            'user_id' => $user->id
        ]);

        SaleItem::create([
            'sale_id' => $sale1->id,
            'product_id' => $products[0]->id,
            'product_name' => $products[0]->name,
            'product_sku' => $products[0]->sku,
            'quantity' => 2,
            'price' => $products[0]->price
        ]);

        $sale1->calculateTotals();

        // Sale 2
        $sale2 = Sale::create([
            'customer_id' => $customers[1]->id,
            'customer_name' => $customers[1]->name,
            'sale_date' => now()->subDays(2),
            'tax' => 50000,
            'discount' => 0,
            'payment_status' => 'pending',
            'payment_method' => null,
            'payment_date' => null,
            'user_id' => $user->id
        ]);

        SaleItem::create([
            'sale_id' => $sale2->id,
            'product_id' => $products[1]->id,
            'product_name' => $products[1]->name,
            'product_sku' => $products[1]->sku,
            'quantity' => 5,
            'price' => $products[1]->price
        ]);

        $sale2->calculateTotals();

        // Sale 3
        $sale3 = Sale::create([
            'customer_id' => $customers[2]->id,
            'customer_name' => $customers[2]->name,
            'sale_date' => now(),
            'tax' => 0,
            'discount' => 50000,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'payment_date' => now(),
            'user_id' => $user->id
        ]);

        SaleItem::create([
            'sale_id' => $sale3->id,
            'product_id' => $products[2]->id,
            'product_name' => $products[2]->name,
            'product_sku' => $products[2]->sku,
            'quantity' => 1,
            'price' => $products[2]->price
        ]);

        $sale3->calculateTotals();

        $this->command->info('Sales seeded successfully!');
    }
}