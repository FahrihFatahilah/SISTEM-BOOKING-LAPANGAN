<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();

        // Create Owner
        $owner = User::create([
            'name' => 'Owner System',
            'email' => 'owner@booking.com',
            'password' => Hash::make('password'),
            'branch_id' => null, // Owner can access all branches
        ]);
        $owner->assignRole('owner');

        // Create Admin for each branch
        foreach ($branches as $branch) {
            $admin = User::create([
                'name' => 'Admin ' . $branch->name,
                'email' => 'admin' . $branch->id . '@booking.com',
                'password' => Hash::make('password'),
                'branch_id' => $branch->id,
            ]);
            $admin->assignRole('admin');

            // Create Staff for each branch
            $staff = User::create([
                'name' => 'Staff ' . $branch->name,
                'email' => 'staff' . $branch->id . '@booking.com',
                'password' => Hash::make('password'),
                'branch_id' => $branch->id,
            ]);
            $staff->assignRole('staff');
        }
    }
}