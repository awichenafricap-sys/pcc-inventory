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
        'description',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function flavors()
    {
        return $this->hasMany(ProductFlavor::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'ingredient_product')->withPivot('measurement')->withTimestamps();
    }

 public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/no-image.png'); // Default image kung walang upload
    }
}
