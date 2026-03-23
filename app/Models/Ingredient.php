<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $table = 'ingredients';
    
    protected $fillable = [
        'name',
        'sku',
        'category_id',
        'unit_of_measurement',
        'current_stock',
        'minimum_stock',
        'cost_per_unit',
        'supplier',
        'location',
        'expiry_date',
        'status',
        'description'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'current_stock' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        'cost_per_unit' => 'decimal:2'
    ];

    // Relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Auto-update status based on stock levels
    protected static function booted()
    {
        static::saving(function ($ingredient) {
            if ($ingredient->current_stock <= 0) {
                $ingredient->status = 'out_of_stock';
            } elseif ($ingredient->current_stock <= $ingredient->minimum_stock) {
                $ingredient->status = 'low_stock';
            } else {
                $ingredient->status = 'in_stock';
            }
        });
    }

    // Scopes for filtering
    public function scopeInStock($query)
    {
        return $query->where('status', 'in_stock');
    }

    public function scopeLowStock($query)
    {
        return $query->where('status', 'low_stock');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('status', 'out_of_stock');
    }

    public function scopeNearExpiry($query, $days = 7)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                     ->where('expiry_date', '>', now());
    }

    // Helper methods
    public function isLowStock(): bool
    {
        return $this->status === 'low_stock';
    }

    public function isOutOfStock(): bool
    {
        return $this->status === 'out_of_stock';
    }

    public function getStockStatusAttribute(): string
    {
        return match($this->status) {
            'out_of_stock' => 'Out of Stock',
            'low_stock' => 'Low Stock',
            default => 'In Stock'
        };
    }

    public function getStockStatusColorAttribute(): string
    {
        return match($this->status) {
            'out_of_stock' => 'danger',
            'low_stock' => 'warning',
            default => 'success'
        };
    }

    public function getFormattedCostAttribute(): string
    {
        return $this->cost_per_unit 
            ? '₱' . number_format($this->cost_per_unit, 2) 
            : 'N/A';
    }
}