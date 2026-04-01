<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Field extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'name',
        'description',
        'price_per_hour',
        'is_active'
    ];

    protected $casts = [
        'price_per_hour' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function pricingRules(): HasMany
    {
        return $this->hasMany(PricingRule::class);
    }

    public function memberSchedules(): HasMany
    {
        return $this->hasMany(MemberSchedule::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isAvailable($date, $startTime, $endTime, $excludeBookingId = null)
    {
        // Check all bookings (regular and membership)
        $query = $this->bookings()
            ->where('booking_date', $date)
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                  ->orWhereBetween('end_time', [$startTime, $endTime])
                  ->orWhere(function ($q2) use ($startTime, $endTime) {
                      $q2->where('start_time', '<=', $startTime)
                         ->where('end_time', '>=', $endTime);
                  });
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->count() === 0;
    }

    public function getPriceForDateTime($date, $time)
    {
        $dayOfWeek = \Carbon\Carbon::parse($date)->dayOfWeek;
        return PricingRule::getPriceForDateTime($this->id, $dayOfWeek, $time);
    }

    public function getConflictingSchedules($date, $startTime, $endTime, $excludeBookingId = null)
    {
        $conflicts = [];
        
        // Check all bookings (regular and membership)
        $bookings = $this->bookings()
            ->where('booking_date', $date)
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                  ->orWhereBetween('end_time', [$startTime, $endTime])
                  ->orWhere(function ($q2) use ($startTime, $endTime) {
                      $q2->where('start_time', '<=', $startTime)
                         ->where('end_time', '>=', $endTime);
                  });
            })
            ->when($excludeBookingId, function($q) use ($excludeBookingId) {
                $q->where('id', '!=', $excludeBookingId);
            })
            ->get();
            
        foreach ($bookings as $booking) {
            $conflicts[] = [
                'type' => $booking->is_membership ? 'membership' : 'regular',
                'customer' => $booking->customer_name,
                'time' => $booking->start_time . ' - ' . $booking->end_time,
                'status' => $booking->is_membership ? 'membership' : $booking->status
            ];
        }
        
        return $conflicts;
    }

    public function getCurrentPrice()
    {
        $now = now();
        return $this->getPriceForDateTime($now->toDateString(), $now->format('H:i:s'));
    }
}