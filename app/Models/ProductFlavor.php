<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFlavor extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'flavor_name',
        'measurement',
        'ingredients_text',
        'batch',
        'is_active',
        'in_items',
    ];

    protected $casts = [
        'batch' => 'integer',
        'is_active' => 'boolean',
        'in_items' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sizes()
    {
        return $this->hasMany(ProductSize::class);
    }

    public function recipeItems()
    {
        return $this->hasMany(ProductRecipeItem::class);
    }

    public function productionSchedules()
    {
        return $this->hasMany(ProductionSchedule::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
