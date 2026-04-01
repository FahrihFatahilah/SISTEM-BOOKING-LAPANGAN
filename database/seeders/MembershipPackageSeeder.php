<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MembershipPackage;

class MembershipPackageSeeder extends Seeder
{
    public function run()
    {
        $packages = [
            [
                'name' => 'Paket Basic',
                'description' => 'Paket membership dasar untuk pemain reguler',
                'price' => 300000,
                'duration_days' => 30,
                'sessions_per_week' => 2,
                'session_duration_hours' => 1.5,
                'is_active' => true
            ],
            [
                'name' => 'Paket Premium',
                'description' => 'Paket membership premium dengan lebih banyak sesi',
                'price' => 500000,
                'duration_days' => 30,
                'sessions_per_week' => 3,
                'session_duration_hours' => 2,
                'is_active' => true
            ],
            [
                'name' => 'Paket VIP',
                'description' => 'Paket membership VIP untuk pemain intensif',
                'price' => 750000,
                'duration_days' => 30,
                'sessions_per_week' => 4,
                'session_duration_hours' => 2,
                'is_active' => true
            ]
        ];

        foreach ($packages as $package) {
            MembershipPackage::create($package);
        }
    }
}