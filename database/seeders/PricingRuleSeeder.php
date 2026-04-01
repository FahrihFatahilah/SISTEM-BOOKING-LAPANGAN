<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PricingRule;
use App\Models\Field;

class PricingRuleSeeder extends Seeder
{
    public function run()
    {
        $fields = Field::all();
        
        foreach ($fields as $field) {
            // Prime Time - Weekdays Evening
            PricingRule::create([
                'field_id' => $field->id,
                'rule_name' => 'Prime Time Weekdays',
                'description' => 'Harga prime time untuk hari kerja sore-malam',
                'days_of_week' => [1, 2, 3, 4, 5], // Senin-Jumat
                'start_time' => '18:00',
                'end_time' => '22:00',
                'price_per_hour' => $field->price_per_hour * 1.5, // 50% lebih mahal
                'priority' => 8,
                'is_active' => true
            ]);
            
            // Weekend Premium
            PricingRule::create([
                'field_id' => $field->id,
                'rule_name' => 'Weekend Premium',
                'description' => 'Harga premium untuk weekend',
                'days_of_week' => [0, 6], // Sabtu-Minggu
                'start_time' => '08:00',
                'end_time' => '22:00',
                'price_per_hour' => $field->price_per_hour * 1.3, // 30% lebih mahal
                'priority' => 7,
                'is_active' => true
            ]);
            
            // Happy Hour - Weekdays Afternoon
            PricingRule::create([
                'field_id' => $field->id,
                'rule_name' => 'Happy Hour',
                'description' => 'Harga diskon untuk siang hari kerja',
                'days_of_week' => [1, 2, 3, 4, 5], // Senin-Jumat
                'start_time' => '13:00',
                'end_time' => '17:00',
                'price_per_hour' => $field->price_per_hour * 0.8, // 20% lebih murah
                'priority' => 5,
                'is_active' => true
            ]);
        }
    }
}