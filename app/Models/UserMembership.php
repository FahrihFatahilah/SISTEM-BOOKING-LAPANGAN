<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'field_id',
        'start_date',
        'day_of_week',
        'start_time',
        'session_duration_hours',
        'monthly_price',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'start_time' => 'datetime:H:i',
        'session_duration_hours' => 'decimal:1',
        'monthly_price' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function membershipBookings()
    {
        return $this->hasMany(MembershipBooking::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('start_date', '<=', now());
    }

    public function isActive()
    {
        return $this->status === 'active' && $this->start_date <= now();
    }

    public function getDayNameAttribute()
    {
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        return $days[$this->day_of_week] ?? '';
    }

    public function generateMonthlyBookings($month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        
        $bookings = [];
        $startOfMonth = Carbon::create($year, $month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        
        $current = $startOfMonth->copy();
        
        while ($current <= $endOfMonth) {
            if ($current->dayOfWeek === $this->day_of_week && $current >= $this->start_date) {
                $bookings[] = [
                    'user_membership_id' => $this->id,
                    'booking_date' => $current->format('Y-m-d'),
                    'start_time' => $this->start_time,
                    'end_time' => $current->copy()->setTimeFromTimeString($this->start_time)
                                         ->addHours($this->session_duration_hours)
                                         ->format('H:i:s'),
                    'status' => 'scheduled',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            $current->addDay();
        }
        
        if (!empty($bookings)) {
            MembershipBooking::insert($bookings);
        }
        
        return count($bookings);
    }
}