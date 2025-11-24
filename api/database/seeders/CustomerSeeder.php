<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $this->command->error('No user found! Please create a user first.');
            return;
        }

        $customers = [
            [
                'name' => 'PT ABC Indonesia',
                'email' => 'contact@abc.com',
                'phone' => '021-12345678',
                'company' => 'PT ABC Indonesia',
                'address' => 'Jl. Sudirman No. 123',
                'city' => 'Jakarta',
                'state' => 'DKI Jakarta',
                'postal_code' => '12345',
                'country' => 'Indonesia',
                'type' => 'company',
                'user_id' => $user->id
            ],
            [
                'name' => 'PT XYZ Corporation',
                'email' => 'info@xyz.com',
                'phone' => '021-87654321',
                'company' => 'PT XYZ Corporation',
                'address' => 'Jl. Thamrin No. 456',
                'city' => 'Jakarta',
                'state' => 'DKI Jakarta',
                'postal_code' => '12346',
                'country' => 'Indonesia',
                'type' => 'company',
                'user_id' => $user->id
            ],
            [
                'name' => 'CV DEF Trading',
                'email' => 'sales@def.com',
                'phone' => '021-11223344',
                'company' => 'CV DEF Trading',
                'address' => 'Jl. Gatot Subroto No. 789',
                'city' => 'Jakarta',
                'state' => 'DKI Jakarta',
                'postal_code' => '12347',
                'country' => 'Indonesia',
                'type' => 'company',
                'user_id' => $user->id
            ]
        ];

        foreach ($customers as $customerData) {
            // ✅ Use firstOrCreate to avoid duplicate
            Customer::firstOrCreate(
                ['email' => $customerData['email']], // Check by email
                $customerData // Create with full data if not exists
            );
        }

        $this->command->info('Customers seeded successfully!');
    }
}