<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngredientDailyMovement extends Model
{
    protected $fillable = [
        'ingredient_id',
        'movement_date',
        'in_items',
        'total_out',
        'ending',
    ];

    protected $casts = [
        'movement_date' => 'date',
        'in_items' => 'decimal:2',
        'total_out' => 'decimal:2',
        'ending' => 'decimal:2',
    ];

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
