<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_days',
        'sessions_per_week',
        'session_duration_hours',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'session_duration_hours' => 'decimal:1'
    ];

    public function userMemberships()
    {
        return $this->hasMany(UserMembership::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}