<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'slug', 
        'description', 
        'color'
    ];

    // Relationship with Ingredients
    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }

    // Auto-generate slug from name
    protected static function booted()
    {
        static::creating(function ($category) {
            $category->slug = str($category->name)->slug();
        });
    }
}