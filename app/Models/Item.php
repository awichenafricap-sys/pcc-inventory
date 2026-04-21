<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name','cost_per_unit','is_default','default_quantity','stock','image'];

    /**
     * Attribute casting
     * Store `is_default` as integer (0 or 1) and ensure proper types for others.
     */
    protected $casts = [
        'is_default' => 'integer',
        'cost_per_unit' => 'decimal:2',
        'stock' => 'integer',
        'default_quantity' => 'integer',
    ];
}
