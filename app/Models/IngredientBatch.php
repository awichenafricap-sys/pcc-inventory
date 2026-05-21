<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingredient_id',
        'batch_number',
        'quantity',
        'remaining_quantity',
        'cost_per_unit',
        'received_date',
        'expiry_date',
        'supplier',
        'status',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'remaining_quantity' => 'decimal:2',
        'cost_per_unit' => 'decimal:2',
        'received_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function productionConsumptions()
    {
        return $this->hasMany(ProductionConsumption::class);
    }

    public function scopeAvailable($query)
    {
        return $query->whereIn('status', ['available', 'partial']);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
                     ->where('expiry_date', '<=', now()->addDays($days))
                     ->where('expiry_date', '>', now());
    }
}
