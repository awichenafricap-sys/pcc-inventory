<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produce extends Model
{
    use HasFactory;

    protected $table = 'produce';

    protected $fillable = [
        'name',
        'description',
        'category',
        'product_id',
        'quantity',
        'produced_at',
        'produced_at_datetime',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'produced_at' => 'date',
        'produced_at_datetime' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batchUsages()
    {
        return $this->hasMany(ProduceBatchUsage::class);
    }
}
