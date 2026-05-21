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
        'minimum_stock',
        'cost_per_unit',
        'supplier',
        'location',
        'description',
        'is_active',
        'beginning_inventory',
        'in_items',
        'date_receive',
        'receive_items',
        'actual_ending',
        'released_used_items',
    ];

    protected $casts = [
        'minimum_stock' => 'decimal:2',
        'cost_per_unit' => 'decimal:2',
        'is_active' => 'boolean',
        'beginning_inventory' => 'decimal:2',
        'in_items' => 'decimal:2',
        'receive_items' => 'decimal:2',
        'actual_ending' => 'decimal:2',
        'released_used_items' => 'decimal:2',
        'date_receive' => 'date',
    ];

    // Relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function batches()
    {
        return $this->hasMany(IngredientBatch::class);
    }

    public function stockView()
    {
        return $this->hasOne(IngredientStockView::class, 'ingredient_id', 'id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'ingredient_product')->withPivot('measurement')->withTimestamps();
    }

    public function recipeItems()
    {
        return $this->hasMany(ProductRecipeItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getCurrentStockAttribute(): float
    {
        return $this->batches()
            ->whereIn('status', ['available', 'partial'])
            ->sum('remaining_quantity');
    }

    public function getStockStatusAttribute(): string
    {
        $stock = $this->current_stock;
        if ($stock <= 0) return 'Out of Stock';
        if ($stock <= $this->minimum_stock) return 'Low Stock';
        return 'In Stock';
    }

    public function getStockStatusColorAttribute(): string
    {
        $stock = $this->current_stock;
        if ($stock <= 0) return 'danger';
        if ($stock <= $this->minimum_stock) return 'warning';
        return 'success';
    }

    public function getFormattedCostAttribute(): string
    {
        return $this->cost_per_unit 
            ? '₱' . number_format($this->cost_per_unit, 2) 
            : 'N/A';
    }
}