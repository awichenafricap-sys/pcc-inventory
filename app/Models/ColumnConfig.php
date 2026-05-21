<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ColumnConfig extends Model
{
    protected $fillable = [
        'type',
        'column_name',
        'column_label',
        'sort_order',
        'is_active',
        'divisor_type',
        'divisor_value',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'divisor_value' => 'float',
    ];

    public function scopeForType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
