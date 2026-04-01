<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CustomerUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create some customer users
        $customers = [
            [
                'name' => 'John Doe',
                'email' => 'john@customer.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@customer.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Bob Wilson',
                'email' => 'bob@customer.com',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($customers as $customerData) {
            $customer = User::create($customerData);
            $customer->assignRole('customer');
            
            $this->command->info("Customer {$customer->name} created successfully.");
        }
    }
}