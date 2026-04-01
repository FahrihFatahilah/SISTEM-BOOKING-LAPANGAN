<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_membership_id',
        'booking_date',
        'start_time',
        'end_time',
        'status'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s'
    ];

    public function userMembership()
    {
        return $this->belongsTo(UserMembership::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('booking_date', '>=', now()->toDateString())
                    ->where('status', 'scheduled');
    }

    public function scopeToday($query)
    {
        return $query->where('booking_date', now()->toDateString());
    }
}