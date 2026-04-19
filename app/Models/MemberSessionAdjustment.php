<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberSessionAdjustment extends Model
{
    protected $fillable = [
        'member_schedule_id',
        'type',
        'booking_id',
        'reason',
        'adjusted_by',
    ];

    public function memberSchedule()
    {
        return $this->belongsTo(MemberSchedule::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function adjustedByUser()
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }
}
