<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Field;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        // Create branches
        $branch1 = Branch::create([
            'name' => 'Cabang Pusat Jakarta',
            'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
            'phone' => '021-12345678',
            'open_time' => '06:00',
            'close_time' => '22:00',
            'is_active' => true,
        ]);

        $branch2 = Branch::create([
            'name' => 'Cabang Bekasi',
            'address' => 'Jl. Ahmad Yani No. 456, Bekasi',
            'phone' => '021-87654321',
            'open_time' => '07:00',
            'close_time' => '21:00',
            'is_active' => true,
        ]);

        // Create fields for branch 1
        Field::create([
            'branch_id' => $branch1->id,
            'name' => 'Lapangan A',
            'description' => 'Lapangan futsal standar FIFA',
            'price_per_hour' => 150000,
            'is_active' => true,
        ]);

        Field::create([
            'branch_id' => $branch1->id,
            'name' => 'Lapangan B',
            'description' => 'Lapangan futsal dengan rumput sintetis',
            'price_per_hour' => 175000,
            'is_active' => true,
        ]);

        // Create fields for branch 2
        Field::create([
            'branch_id' => $branch2->id,
            'name' => 'Lapangan 1',
            'description' => 'Lapangan badminton indoor',
            'price_per_hour' => 80000,
            'is_active' => true,
        ]);

        Field::create([
            'branch_id' => $branch2->id,
            'name' => 'Lapangan 2',
            'description' => 'Lapangan basket outdoor',
            'price_per_hour' => 120000,
            'is_active' => true,
        ]);
    }
}