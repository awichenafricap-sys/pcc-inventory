<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduceBatchUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'produce_id',
        'restock_id',
        'item_id',
        'quantity_used',
    ];

    public function produce()
    {
        return $this->belongsTo(Produce::class);
    }

    public function restock()
    {
        return $this->belongsTo(Restock::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
