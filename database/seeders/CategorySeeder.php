<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Fruits', 'slug' => 'fruits', 'description' => 'Fresh and concentrated fruits', 'color' => '#FF5733'],
            ['name' => 'Sweeteners', 'slug' => 'sweeteners', 'description' => 'Sugar, honey, artificial sweeteners', 'color' => '#33FF57'],
            ['name' => 'Acids', 'slug' => 'acids', 'description' => 'Citric acid, ascorbic acid', 'color' => '#3357FF'],
            ['name' => 'Preservatives', 'slug' => 'preservatives', 'description' => 'Preservatives and stabilizers', 'color' => '#FF33F5'],
            ['name' => 'Water', 'slug' => 'water', 'description' => 'Purified water', 'color' => '#33FFF5'],
            ['name' => 'Dairy', 'slug' => 'dairy', 'description' => 'Milk, cream, yogurt base', 'color' => '#FFF333'],
            ['name' => 'Powders', 'slug' => 'powders', 'description' => 'Cocoa, coffee, tea powder', 'color' => '#8B4513'],
            ['name' => 'Flavor', 'slug' => 'flavor', 'description' => 'Flavorings and extracts', 'color' => '#FF69B4'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        $this->command->info('Categories seeded successfully!');
    }
}
