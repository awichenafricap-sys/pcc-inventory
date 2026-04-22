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
    'type',
    'unit',
    'container_size_ml',
    'beginning',
    'current_stock',
    'reorder_level',
    'cost',
    'ending',
    'description',
    'image'
];

public function productionSchedules()
{
    return $this->hasMany(ProductionSchedule::class);
}

public function flavors()
{
    return $this->hasMany(ProductFlavor::class);
}

 public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/no-image.png'); // Default image kung walang upload
    }
}
