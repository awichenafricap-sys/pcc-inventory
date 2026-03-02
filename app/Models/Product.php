<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
    'code',
    'name',
    'category',
    'unit',
    'current_stock',
    'reorder_level',
    'description',
    'image'
];
 public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/no-image.png'); // Default image kung walang upload
    }
}
