<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restock extends Model
{
    use HasFactory;

    protected $table = 'restock';

    protected $fillable = [
        'item_id',
        'quantity',
        'restock_date',
        'batch_code',
        'batch_date',
        'notes',
    ];

    protected $casts = [
        'restock_date' => 'datetime',
        'batch_date' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function produceBatchUsages()
    {
        return $this->hasMany(ProduceBatchUsage::class);
    }
}
