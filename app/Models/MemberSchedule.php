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
        'monthly_limit',
        'start_date',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
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

    // Generate sesi booking dari start_date berdasarkan monthly_limit
    public function generateBookingsFor30Days($startDate = null)
    {
        $memberStart = Carbon::parse($this->start_date);
        $maxSessions = $this->monthly_limit ?? 4;
        $field = Field::find($this->field_id);

        // Jump langsung ke hari yang benar
        $current = $memberStart->copy();
        if ($current->dayOfWeek != $this->day_of_week) {
            $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            $current->next($dayNames[$this->day_of_week]);
        }

        // Generate 4 tanggal sekaligus
        $dates = [];
        for ($i = 0; $i < $maxSessions; $i++) {
            $dates[] = $current->copy()->addWeeks($i)->format('Y-m-d');
        }

        // Cek existing sekali aja (1 query)
        $existingDates = Booking::where('field_id', $this->field_id)
            ->where('customer_name', $this->member_name)
            ->where('is_membership', true)
            ->whereIn('booking_date', $dates)
            ->pluck('booking_date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        // Bulk prepare
        $toInsert = [];
        $sessionNum = count($existingDates);
        foreach ($dates as $date) {
            if (!in_array($date, $existingDates)) {
                $sessionNum++;
                // Harga per sesi: pakai harga lapangan sesuai weekday/weekend
                $pricePerSession = $field
                    ? $field->getPriceForDate($date) * abs(Carbon::parse($this->end_time)->diffInHours(Carbon::parse($this->start_time)))
                    : $this->monthly_price / $maxSessions;

                $toInsert[] = [
                    'field_id' => $this->field_id,
                    'user_id' => auth()->id() ?? 1,
                    'customer_name' => $this->member_name,
                    'customer_phone' => $this->member_phone,
                    'booking_date' => $date,
                    'start_time' => $this->start_time,
                    'end_time' => $this->end_time,
                    'total_price' => $pricePerSession,
                    'status' => 'pending',
                    'is_membership' => true,
                    'booking_type' => 'member',
                    'notes' => "Sesi member {$sessionNum}/{$maxSessions}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($toInsert)) {
            Booking::insert($toInsert);
        }

        return Booking::where('field_id', $this->field_id)
            ->where('customer_name', $this->member_name)
            ->where('is_membership', true)
            ->whereIn('booking_date', $dates)
            ->get();
    }

    // Auto-complete sesi yang sudah lewat
    public function autoCompletePastSessions()
    {
        return \App\Models\Booking::where('field_id', $this->field_id)
            ->where('customer_name', $this->member_name)
            ->where('is_membership', true)
            ->where('status', 'pending')
            ->where('booking_date', '<', now()->format('Y-m-d'))
            ->update(['status' => 'completed']);
    }

    // Cek sisa kuota: hitung sesi pending (belum selesai/batal)
    public function getRemainingQuota($month = null, $year = null)
    {
        return Booking::where('field_id', $this->field_id)
            ->where('customer_name', $this->member_name)
            ->where('is_membership', true)
            ->where('booking_date', '>=', Carbon::parse($this->start_date)->format('Y-m-d'))
            ->where('status', 'pending')
            ->count();
    }
    
    // Cek apakah member masih bisa booking (ada slot untuk generate baru)
    public function canBookThisMonth($month = null, $year = null)
    {
        $limit = $this->monthly_limit ?? 4;
        $total = Booking::where('field_id', $this->field_id)
            ->where('customer_name', $this->member_name)
            ->where('is_membership', true)
            ->where('booking_date', '>=', Carbon::parse($this->start_date)->format('Y-m-d'))
            ->whereIn('status', ['pending', 'completed'])
            ->count();

        return $total < $limit;
    }
    
    // Get tanggal sesi terakhir
    public function getEndDateAttribute()
    {
        $last = Booking::where('field_id', $this->field_id)
            ->where('customer_name', $this->member_name)
            ->where('is_membership', true)
            ->where('booking_date', '>=', Carbon::parse($this->start_date)->format('Y-m-d'))
            ->orderBy('booking_date', 'desc')
            ->first();

        return $last ? $last->booking_date : Carbon::parse($this->start_date);
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