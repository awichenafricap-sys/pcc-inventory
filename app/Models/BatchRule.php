<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingredient_product_id',
        'batch_limit',
        'measurement',
    ];
}
