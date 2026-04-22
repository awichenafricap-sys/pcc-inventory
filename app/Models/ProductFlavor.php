<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFlavor extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'flavor_name',
        'measurement',
        'sizes',
        'ingredients',
        'batch',
        'qty_200ml',
        'qty_500ml',
        'qty_1000ml',
    ];

    protected $casts = [
        'batch' => 'integer',
        'qty_200ml' => 'integer',
        'qty_500ml' => 'integer',
        'qty_1000ml' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
