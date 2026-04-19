<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'code', 'name', 'description', 'purchase_price', 'selling_price',
        'stock', 'warehouse_stock', 'display_stock', 'min_stock', 'branch_id', 'is_active'
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function stockTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock');
    }

    public function reduceStock(int $quantity): void
    {
        $this->decrement('display_stock', $quantity);
        $this->decrement('stock', $quantity);
    }

    public function addStock(int $quantity): void
    {
        $this->increment('warehouse_stock', $quantity);
        $this->increment('stock', $quantity);
    }

    public function transferToDisplay(int $quantity): bool
    {
        if ($this->warehouse_stock < $quantity) {
            return false;
        }

        $this->decrement('warehouse_stock', $quantity);
        $this->increment('display_stock', $quantity);

        return true;
    }

    public function getTodayDisplayStockAttribute(): int
    {
        $todayTransfer = $this->stockTransfers()
            ->whereDate('transfer_date', today())
            ->sum('quantity');

        return $todayTransfer;
    }
}
