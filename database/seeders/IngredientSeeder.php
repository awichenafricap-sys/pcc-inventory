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
            ['name' => 'Dairy', 'slug' => 'dairy', 'description' => 'Milk, cheese, butter'],
            ['name' => 'Vegetables', 'slug' => 'vegetables', 'description' => 'Fresh produce'],
        ];
        
        $categoryIds = [];
        foreach ($categories as $cat) {
            $category = Category::firstOrCreate(['slug' => $cat['slug']], $cat);
            $categoryIds[$cat['slug']] = $category->id;
        }
        
        // Create sample ingredients with new fields
        $ingredients = [
            [
                'name' => 'All-Purpose Flour',
                'sku' => 'BAK-001',
                'category_id' => $categoryIds['baking-goods'],
                'unit_of_measurement' => 'kg',
                'beginning_inventory' => 50,
                'received_quantity' => 20,
                'current_stock' => 60,
                'ending_inventory' => 10,
                'cost_per_unit' => 45.00,
                'supplier' => 'ABC Trading',
                'location' => 'Warehouse A',
            ],
            [
                'name' => 'White Sugar',
                'sku' => 'BAK-002',
                'category_id' => $categoryIds['baking-goods'],
                'unit_of_measurement' => 'kg',
                'beginning_inventory' => 30,
                'received_quantity' => 15,
                'current_stock' => 40,
                'ending_inventory' => 5,
                'cost_per_unit' => 60.00,
                'supplier' => 'Sugar Plus Inc',
                'location' => 'Warehouse A',
            ],
            [
                'name' => 'Vegetable Oil',
                'sku' => 'LIQ-001',
                'category_id' => $categoryIds['liquids'],
                'unit_of_measurement' => 'liters',
                'beginning_inventory' => 25,
                'received_quantity' => 10,
                'current_stock' => 30,
                'ending_inventory' => 5,
                'cost_per_unit' => 85.00,
                'supplier' => 'Oil Masters',
                'location' => 'Warehouse B',
            ],
            [
                'name' => 'Soy Sauce',
                'sku' => 'LIQ-002',
                'category_id' => $categoryIds['liquids'],
                'unit_of_measurement' => 'liters',
                'beginning_inventory' => 10,
                'received_quantity' => 5,
                'current_stock' => 12,
                'ending_inventory' => 3,
                'cost_per_unit' => 120.00,
                'supplier' => 'Asian Foods Co',
                'location' => 'Warehouse B',
            ],
            [
                'name' => 'Iodized Salt',
                'sku' => 'SPC-001',
                'category_id' => $categoryIds['spices'],
                'unit_of_measurement' => 'kg',
                'beginning_inventory' => 15,
                'received_quantity' => 5,
                'current_stock' => 18,
                'ending_inventory' => 2,
                'cost_per_unit' => 35.00,
                'supplier' => 'Salt Factory',
                'location' => 'Warehouse C',
            ],
            [
                'name' => 'Black Pepper',
                'sku' => 'SPC-002',
                'category_id' => $categoryIds['spices'],
                'unit_of_measurement' => 'kg',
                'beginning_inventory' => 5,
                'received_quantity' => 2,
                'current_stock' => 6,
                'ending_inventory' => 1,
                'cost_per_unit' => 250.00,
                'supplier' => 'Spice World',
                'location' => 'Warehouse C',
            ],
            [
                'name' => 'Fresh Milk',
                'sku' => 'DRY-001',
                'category_id' => $categoryIds['dairy'],
                'unit_of_measurement' => 'liters',
                'beginning_inventory' => 20,
                'received_quantity' => 10,
                'current_stock' => 25,
                'ending_inventory' => 5,
                'cost_per_unit' => 95.00,
                'supplier' => 'Dairy Fresh',
                'location' => 'Cold Storage',
                'expiry_date' => now()->addDays(14),
            ],
            [
                'name' => 'Butter',
                'sku' => 'DRY-002',
                'category_id' => $categoryIds['dairy'],
                'unit_of_measurement' => 'kg',
                'beginning_inventory' => 8,
                'received_quantity' => 4,
                'current_stock' => 10,
                'ending_inventory' => 2,
                'cost_per_unit' => 180.00,
                'supplier' => 'Dairy Fresh',
                'location' => 'Cold Storage',
            ],
            [
                'name' => 'Onions',
                'sku' => 'VEG-001',
                'category_id' => $categoryIds['vegetables'],
                'unit_of_measurement' => 'kg',
                'beginning_inventory' => 40,
                'received_quantity' => 20,
                'current_stock' => 50,
                'ending_inventory' => 10,
                'cost_per_unit' => 70.00,
                'supplier' => 'Farm Fresh',
                'location' => 'Warehouse D',
            ],
            [
                'name' => 'Garlic',
                'sku' => 'VEG-002',
                'category_id' => $categoryIds['vegetables'],
                'unit_of_measurement' => 'kg',
                'beginning_inventory' => 12,
                'received_quantity' => 8,
                'current_stock' => 15,
                'ending_inventory' => 5,
                'cost_per_unit' => 150.00,
                'supplier' => 'Farm Fresh',
                'location' => 'Warehouse D',
            ],
        ];
        
        foreach ($ingredients as $ingredient) {
            Ingredient::firstOrCreate(['sku' => $ingredient['sku']], $ingredient);
        }
        
        $this->command->info('Ingredients seeded successfully!');
    }
}
