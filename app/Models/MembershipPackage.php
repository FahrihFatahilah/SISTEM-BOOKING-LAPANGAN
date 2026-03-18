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
        'booking_quota',
        'discount_percentage',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'is_active' => 'boolean'
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