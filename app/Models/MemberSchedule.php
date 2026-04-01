<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MemberSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_name',
        'member_phone',
        'field_id',
        'day_of_week',
        'start_time',
        'end_time',
        'monthly_price',
        'start_date',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'monthly_price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function getDayNameAttribute()
    {
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        return $days[$this->day_of_week] ?? '';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Generate booking untuk 4 sesi dalam sebulan (bukan setiap minggu)
    public function generateBookingsFor30Days($startDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now();
        $endDate = $startDate->copy()->addDays(30);
        
        $bookings = [];
        $current = $startDate->copy();
        $sessionCount = 0;
        $maxSessions = 4; // Batas 4 sesi per bulan
        
        while ($current <= $endDate && $sessionCount < $maxSessions) {
            if ($current->dayOfWeek === $this->day_of_week && $current >= $this->start_date) {
                // Cek apakah sudah ada booking di tanggal ini
                $existingBooking = Booking::where('field_id', $this->field_id)
                    ->where('booking_date', $current->format('Y-m-d'))
                    ->where('start_time', $this->start_time)
                    ->where('end_time', $this->end_time)
                    ->where('is_membership', true)
                    ->where('customer_name', $this->member_name)
                    ->first();
                
                if (!$existingBooking) {
                    $booking = Booking::create([
                        'field_id' => $this->field_id,
                        'user_id' => auth()->id() ?? 1,
                        'customer_name' => $this->member_name,
                        'customer_phone' => $this->member_phone,
                        'booking_date' => $current->format('Y-m-d'),
                        'start_time' => $this->start_time,
                        'end_time' => $this->end_time,
                        'total_price' => 0, // Member sudah bayar bulanan
                        'status' => 'pending',
                        'is_membership' => true,
                        'booking_type' => 'member',
                        'notes' => "Sesi member {$sessionCount + 1}/4 - Auto-generated"
                    ]);
                    
                    $bookings[] = $booking;
                    $sessionCount++;
                }
            }
            $current->addDay();
        }
        
        return $bookings;
    }

    // Cek sisa kuota member untuk bulan ini
    public function getRemainingQuota($month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        
        $usedQuota = Booking::where('field_id', $this->field_id)
            ->where('customer_name', $this->member_name)
            ->where('is_membership', true)
            ->whereMonth('booking_date', $month)
            ->whereYear('booking_date', $year)
            ->count();
            
        return 4 - $usedQuota;
    }
    
    // Cek apakah member masih bisa booking di bulan ini
    public function canBookThisMonth($month = null, $year = null)
    {
        return $this->getRemainingQuota($month, $year) > 0;
    }
    
    // Get booking history untuk member ini
    public function getMonthlyBookings($month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        
        return Booking::where('field_id', $this->field_id)
            ->where('customer_name', $this->member_name)
            ->where('is_membership', true)
            ->whereMonth('booking_date', $month)
            ->whereYear('booking_date', $year)
            ->orderBy('booking_date')
            ->get();
    }
}