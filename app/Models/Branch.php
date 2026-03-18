<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'open_time',
        'close_time',
        'is_active'
    ];

    protected $casts = [
        'open_time' => 'datetime:H:i',
        'close_time' => 'datetime:H:i',
        'is_active' => 'boolean'
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function bookings()
    {
        return $this->hasManyThrough(Booking::class, Field::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}