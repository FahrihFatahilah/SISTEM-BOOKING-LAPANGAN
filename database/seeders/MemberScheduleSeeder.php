<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MemberSchedule;
use App\Models\Field;
use Carbon\Carbon;

class MemberScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $fields = Field::all();
        
        if ($fields->isEmpty()) {
            $this->command->info('No fields found. Please run BranchSeeder first.');
            return;
        }

        $memberSchedules = [
            [
                'member_name' => 'John Doe',
                'member_phone' => '081234567890',
                'field_id' => $fields->first()->id,
                'day_of_week' => 1, // Senin
                'start_time' => '14:00',
                'end_time' => '16:00',
                'monthly_price' => 1200000, // 1.2 juta per bulan
                'start_date' => Carbon::now()->startOfMonth(),
                'notes' => 'Member tetap setiap Senin'
            ],
            [
                'member_name' => 'John Doe',
                'member_phone' => '081234567890',
                'field_id' => $fields->first()->id,
                'day_of_week' => 3, // Rabu
                'start_time' => '14:00',
                'end_time' => '16:00',
                'monthly_price' => 1200000,
                'start_date' => Carbon::now()->startOfMonth(),
                'notes' => 'Member tetap setiap Rabu'
            ],
            [
                'member_name' => 'Jane Smith',
                'member_phone' => '081234567891',
                'field_id' => $fields->count() > 1 ? $fields->skip(1)->first()->id : $fields->first()->id,
                'day_of_week' => 2, // Selasa
                'start_time' => '16:00',
                'end_time' => '18:00',
                'monthly_price' => 1000000,
                'start_date' => Carbon::now()->startOfMonth(),
                'notes' => 'Member tetap setiap Selasa'
            ],
            [
                'member_name' => 'Jane Smith',
                'member_phone' => '081234567891',
                'field_id' => $fields->count() > 1 ? $fields->skip(1)->first()->id : $fields->first()->id,
                'day_of_week' => 5, // Jumat
                'start_time' => '16:00',
                'end_time' => '18:00',
                'monthly_price' => 1000000,
                'start_date' => Carbon::now()->startOfMonth(),
                'notes' => 'Member tetap setiap Jumat'
            ],
            [
                'member_name' => 'Bob Wilson',
                'member_phone' => '081234567892',
                'field_id' => $fields->first()->id,
                'day_of_week' => 6, // Sabtu
                'start_time' => '08:00',
                'end_time' => '10:00',
                'monthly_price' => 800000,
                'start_date' => Carbon::now()->startOfMonth(),
                'notes' => 'Member tetap setiap Sabtu pagi'
            ],
        ];

        foreach ($memberSchedules as $scheduleData) {
            $schedule = MemberSchedule::create($scheduleData);
            
            // Generate booking untuk 30 hari ke depan
            $bookings = $schedule->generateBookingsFor30Days();
            
            $this->command->info("Member schedule created: {$schedule->member_name} - {$schedule->day_name} (" . count($bookings) . " bookings generated)");
        }
    }
}