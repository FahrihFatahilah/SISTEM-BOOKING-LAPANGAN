<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Field;
use Carbon\Carbon;

class MemberBookingSeeder extends Seeder
{
    public function run(): void
    {
        $fields = Field::all();
        
        if ($fields->isEmpty()) {
            $this->command->info('No fields found. Please run BranchSeeder first.');
            return;
        }

        // Contoh booking member untuk 4 hari dalam seminggu
        $memberBookings = [
            [
                'customer_name' => 'John Doe (Member)',
                'customer_phone' => '081234567890',
                'field_id' => $fields->first()->id,
                'booking_date' => Carbon::now()->next(Carbon::MONDAY),
                'start_time' => '14:00',
                'end_time' => '16:00',
                'is_membership' => true,
                'booking_type' => 'member',
            ],
            [
                'customer_name' => 'John Doe (Member)',
                'customer_phone' => '081234567890',
                'field_id' => $fields->first()->id,
                'booking_date' => Carbon::now()->next(Carbon::WEDNESDAY),
                'start_time' => '14:00',
                'end_time' => '16:00',
                'is_membership' => true,
                'booking_type' => 'member',
            ],
            [
                'customer_name' => 'Jane Smith (Member)',
                'customer_phone' => '081234567891',
                'field_id' => $fields->count() > 1 ? $fields->skip(1)->first()->id : $fields->first()->id,
                'booking_date' => Carbon::now()->next(Carbon::TUESDAY),
                'start_time' => '16:00',
                'end_time' => '18:00',
                'is_membership' => true,
                'booking_type' => 'member',
            ],
            [
                'customer_name' => 'Jane Smith (Member)',
                'customer_phone' => '081234567891',
                'field_id' => $fields->count() > 1 ? $fields->skip(1)->first()->id : $fields->first()->id,
                'booking_date' => Carbon::now()->next(Carbon::FRIDAY),
                'start_time' => '16:00',
                'end_time' => '18:00',
                'is_membership' => true,
                'booking_type' => 'member',
            ],
        ];

        foreach ($memberBookings as $bookingData) {
            $field = Field::find($bookingData['field_id']);
            $startTime = Carbon::parse($bookingData['start_time']);
            $endTime = Carbon::parse($bookingData['end_time']);
            $duration = $endTime->diffInHours($startTime);
            $totalPrice = $duration * $field->price_per_hour;

            Booking::create([
                'field_id' => $bookingData['field_id'],
                'user_id' => 1, // Admin user
                'customer_name' => $bookingData['customer_name'],
                'customer_phone' => $bookingData['customer_phone'],
                'booking_date' => $bookingData['booking_date'],
                'start_time' => $bookingData['start_time'],
                'end_time' => $bookingData['end_time'],
                'total_price' => $totalPrice,
                'status' => 'pending',
                'is_membership' => $bookingData['is_membership'],
                'booking_type' => $bookingData['booking_type'],
                'notes' => 'Booking member bulanan'
            ]);

            $this->command->info("Member booking created: {$bookingData['customer_name']} on {$bookingData['booking_date']->format('Y-m-d')}");
        }

        // Contoh booking regular
        $regularBookings = [
            [
                'customer_name' => 'Bob Wilson',
                'customer_phone' => '081234567892',
                'field_id' => $fields->first()->id,
                'booking_date' => Carbon::now()->addDays(1),
                'start_time' => '10:00',
                'end_time' => '12:00',
                'is_membership' => false,
                'booking_type' => 'regular',
            ],
            [
                'customer_name' => 'Alice Brown',
                'customer_phone' => '081234567893',
                'field_id' => $fields->first()->id,
                'booking_date' => Carbon::now()->addDays(2),
                'start_time' => '18:00',
                'end_time' => '20:00',
                'is_membership' => false,
                'booking_type' => 'regular',
            ],
        ];

        foreach ($regularBookings as $bookingData) {
            $field = Field::find($bookingData['field_id']);
            $startTime = Carbon::parse($bookingData['start_time']);
            $endTime = Carbon::parse($bookingData['end_time']);
            $duration = $endTime->diffInHours($startTime);
            $totalPrice = $duration * $field->price_per_hour;

            Booking::create([
                'field_id' => $bookingData['field_id'],
                'user_id' => 1, // Admin user
                'customer_name' => $bookingData['customer_name'],
                'customer_phone' => $bookingData['customer_phone'],
                'booking_date' => $bookingData['booking_date'],
                'start_time' => $bookingData['start_time'],
                'end_time' => $bookingData['end_time'],
                'total_price' => $totalPrice,
                'status' => 'pending',
                'is_membership' => $bookingData['is_membership'],
                'booking_type' => $bookingData['booking_type'],
                'notes' => 'Booking harian'
            ]);

            $this->command->info("Regular booking created: {$bookingData['customer_name']} on {$bookingData['booking_date']->format('Y-m-d')}");
        }
    }
}