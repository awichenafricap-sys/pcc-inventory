<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionConsumption extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_schedule_id',
        'ingredient_batch_id',
        'expected_quantity',
        'actual_quantity',
        'waste_quantity',
        'notes',
    ];

    protected $casts = [
        'expected_quantity' => 'decimal:2',
        'actual_quantity' => 'decimal:2',
        'waste_quantity' => 'decimal:2',
    ];

    public function productionSchedule()
    {
        return $this->belongsTo(ProductionSchedule::class);
    }

    public function ingredientBatch()
    {
        return $this->belongsTo(IngredientBatch::class);
    }

    public function getWastePercentageAttribute(): float
    {
        if ($this->expected_quantity <= 0) return 0;
        return ($this->waste_quantity / $this->expected_quantity) * 100;
    }
}
