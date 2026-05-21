<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDailyBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_name',
        'production_date',
        'type',
        'total_batch',
    ];

    protected $casts = [
        'production_date' => 'date',
        'total_batch' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
