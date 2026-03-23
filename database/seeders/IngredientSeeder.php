<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Ingredient;
    
class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    // Create categories first
    $categories = [
        ['name' => 'Baking Goods', 'slug' => 'baking-goods', 'description' => 'Flour, sugar, etc.'],
        ['name' => 'Liquids', 'slug' => 'liquids', 'description' => 'Oil, water, vinegar'],
        ['name' => 'Spices', 'slug' => 'spices', 'description' => 'Salt, pepper, herbs'],
    ];
    
    foreach ($categories as $cat) {
        Category::firstOrCreate(['slug' => $cat['slug']], $cat);
    }
    
    // Create sample ingredients
    Ingredient::factory(20)->create();
}
}
