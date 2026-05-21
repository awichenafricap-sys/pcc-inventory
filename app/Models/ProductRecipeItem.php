<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRecipeItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_flavor_id',
        'ingredient_id',
        'quantity_required',
        'unit_of_measurement',
        'waste_percentage',
    ];

    protected $casts = [
        'quantity_required' => 'decimal:2',
        'waste_percentage' => 'decimal:2',
    ];

    public function productFlavor()
    {
        return $this->belongsTo(ProductFlavor::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function getEffectiveQuantityAttribute(): float
    {
        return $this->quantity_required * (1 + ($this->waste_percentage / 100));
    }
}
