<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingredient_batch_id',
        'transaction_type',
        'quantity',
        'reference_type',
        'reference_id',
        'previous_balance',
        'new_balance',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'previous_balance' => 'decimal:2',
        'new_balance' => 'decimal:2',
    ];

    public function ingredientBatch()
    {
        return $this->belongsTo(IngredientBatch::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeReceived($query)
    {
        return $query->where('transaction_type', 'received');
    }

    public function scopeReleased($query)
    {
        return $query->where('transaction_type', 'released');
    }
}
