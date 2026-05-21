<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_flavor_id',
        'product_size_id',
        'type',
        'production_date',
        'batch_quantity',
        'status',
        'actual_start_date',
        'actual_end_date',
        'notes',
    ];

    protected $casts = [
        'production_date' => 'date',
        'actual_start_date' => 'datetime',
        'actual_end_date' => 'datetime',
    ];

    public function productFlavor()
    {
        return $this->belongsTo(ProductFlavor::class);
    }

    public function productSize()
    {
        return $this->belongsTo(ProductSize::class);
    }

    public function consumptions()
    {
        return $this->hasMany(ProductionConsumption::class);
    }
}
