<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTracking extends Model
{
    use HasFactory;

    protected $table = 'inventory_trackings';

    protected $fillable = [
        'ingredient_id',
        'beginning',
        'in_released',
        'out',
        'total',
        'ending',
    ];

    protected $casts = [
        'beginning' => 'decimal:2',
        'in_released' => 'decimal:2',
        'total' => 'decimal:2',
        'ending' => 'decimal:2',
    ];

    /**
     * Get the ingredient that owns this tracking record.
     */
    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
