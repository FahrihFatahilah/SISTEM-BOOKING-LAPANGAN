<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Branch;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::first();
        
        if (!$branch) {
            $this->command->warn('No branch found. Please create a branch first.');
            return;
        }

        $products = [
            ['code' => 'PRD001', 'name' => 'Air Mineral 600ml', 'purchase_price' => 3000, 'selling_price' => 5000, 'stock' => 100, 'min_stock' => 20],
            ['code' => 'PRD002', 'name' => 'Teh Botol', 'purchase_price' => 4000, 'selling_price' => 6000, 'stock' => 80, 'min_stock' => 15],
            ['code' => 'PRD003', 'name' => 'Kopi Sachet', 'purchase_price' => 2000, 'selling_price' => 3500, 'stock' => 150, 'min_stock' => 30],
            ['code' => 'PRD004', 'name' => 'Snack Ringan', 'purchase_price' => 5000, 'selling_price' => 8000, 'stock' => 60, 'min_stock' => 10],
            ['code' => 'PRD005', 'name' => 'Energi Drink', 'purchase_price' => 8000, 'selling_price' => 12000, 'stock' => 40, 'min_stock' => 10],
        ];

        foreach ($products as $product) {
            Product::create(array_merge($product, [
                'branch_id' => $branch->id,
                'is_active' => true,
            ]));
        }

        $this->command->info('Products seeded successfully!');
    }
}
