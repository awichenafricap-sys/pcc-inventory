<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IngredientStockView extends Model
{
    protected $table = 'current_ingredient_stock';

    protected $primaryKey = 'ingredient_id';

    public $timestamps = false;

    public $incrementing = false;

    protected $casts = [
        'current_stock' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        'active_batches_count' => 'integer',
        'nearest_expiry_date' => 'datetime',
    ];

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id', 'id');
    }
}
