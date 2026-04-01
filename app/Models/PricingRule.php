<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PricingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_id',
        'days_of_week',
        'start_time',
        'end_time',
        'price_per_hour',
        'rule_name',
        'description',
        'is_active',
        'priority'
    ];

    protected $casts = [
        'days_of_week' => 'array',
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
        'price_per_hour' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDateTime($query, $dayOfWeek, $time)
    {
        return $query->whereJsonContains('days_of_week', $dayOfWeek)
                    ->where('start_time', '<=', $time)
                    ->where('end_time', '>', $time);
    }

    public static function getPriceForDateTime($fieldId, $dayOfWeek, $time)
    {
        $rule = self::where('field_id', $fieldId)
                   ->active()
                   ->forDateTime($dayOfWeek, $time)
                   ->orderBy('priority', 'desc')
                   ->first();

        if ($rule) {
            return $rule->price_per_hour;
        }

        // Fallback ke harga default lapangan
        $field = Field::find($fieldId);
        return $field ? $field->price_per_hour : 0;
    }

    public function getDaysOfWeekTextAttribute()
    {
        $days = [
            0 => 'Minggu',
            1 => 'Senin', 
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu'
        ];

        return collect($this->days_of_week)
               ->map(fn($day) => $days[$day] ?? '')
               ->filter()
               ->join(', ');
    }
}