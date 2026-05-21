<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_flavor_id',
        'column_config_id',
        'size_ml',
        'price',
        'sku',
        'quantity',
        'is_active',
    ];

    protected $casts = [
        'size_ml' => 'integer',
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function productFlavor()
    {
        return $this->belongsTo(ProductFlavor::class);
    }

    public function columnConfig()
    {
        return $this->belongsTo(ColumnConfig::class);
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
