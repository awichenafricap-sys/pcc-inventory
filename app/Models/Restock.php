<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restock extends Model
{
    use HasFactory;

    protected $table = 'restock';

    protected $casts = [
        'restock_date' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
