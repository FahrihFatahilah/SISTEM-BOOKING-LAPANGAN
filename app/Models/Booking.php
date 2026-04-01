<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_id',
        'user_id',
        'customer_name',
        'customer_phone',
        'booking_date',
        'start_time',
        'end_time',
        'total_price',
        'status',
        'notes',
        'is_membership',
        'booking_type'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'total_price' => 'decimal:2',
        'is_membership' => 'boolean'
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('booking_date', today());
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function getStatusBadgeAttribute()
    {
        $typeIcon = $this->is_membership ? '👑' : '🎯';
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">' . $typeIcon . ' 🔵 Akan Datang</span>',
            'ongoing' => '<span class="badge bg-success">' . $typeIcon . ' 🟢 Berjalan</span>',
            'completed' => '<span class="badge bg-secondary">' . $typeIcon . ' 🔴 Selesai</span>',
            'cancelled' => '<span class="badge bg-danger">' . $typeIcon . ' ❌ Dibatalkan</span>',
            default => '<span class="badge bg-light">Unknown</span>'
        };
    }

    public function shouldBeOngoing()
    {
        $now = now();
        $bookingStart = Carbon::parse($this->booking_date->format('Y-m-d') . ' ' . $this->start_time);
        $bookingEnd = Carbon::parse($this->booking_date->format('Y-m-d') . ' ' . $this->end_time);
        
        return $now->between($bookingStart, $bookingEnd);
    }

    public function shouldBeCompleted()
    {
        $now = now();
        $bookingEnd = Carbon::parse($this->booking_date->format('Y-m-d') . ' ' . $this->end_time);
        
        return $now->greaterThan($bookingEnd);
    }
}