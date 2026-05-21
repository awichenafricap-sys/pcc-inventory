<?php

namespace Database\Seeders;

use App\Models\BatchRule;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\IngredientBatch;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = Category::pluck('id', 'slug');

        $ingredients = [
            ['name' => 'Orange Concentrate', 'sku' => 'ING-ORC-001', 'category_slug' => 'fruits', 'unit_of_measurement' => 'kg', 'minimum_stock' => 50, 'cost_per_unit' => 250.00, 'supplier' => 'Citrus Suppliers Inc.', 'location' => 'Warehouse A - Shelf 1', 'description' => 'Frozen orange concentrate', 'beginning_inventory' => 500, 'in_items' => 0, 'is_active' => true],
            ['name' => 'Sugar', 'sku' => 'ING-SUG-001', 'category_slug' => 'sweeteners', 'unit_of_measurement' => 'kg', 'minimum_stock' => 100, 'cost_per_unit' => 45.00, 'supplier' => 'Sugar Corp.', 'location' => 'Warehouse B - Shelf 2', 'description' => 'White refined sugar', 'beginning_inventory' => 1000, 'in_items' => 0, 'is_active' => true],
            ['name' => 'Citric Acid', 'sku' => 'ING-CIT-001', 'category_slug' => 'acids', 'unit_of_measurement' => 'kg', 'minimum_stock' => 10, 'cost_per_unit' => 120.00, 'supplier' => 'Acid Chemicals Co.', 'location' => 'Warehouse A - Shelf 3', 'description' => 'Food grade citric acid', 'beginning_inventory' => 100, 'in_items' => 0, 'is_active' => true],
            ['name' => 'Purified Water', 'sku' => 'ING-WTR-001', 'category_slug' => 'water', 'unit_of_measurement' => 'L', 'minimum_stock' => 500, 'cost_per_unit' => 5.00, 'supplier' => 'Water Systems Inc.', 'location' => 'Tank 1', 'description' => 'RO purified water', 'beginning_inventory' => 10000, 'in_items' => 0, 'is_active' => true],
            ['name' => 'Sodium Benzoate', 'sku' => 'ING-PRV-001', 'category_slug' => 'preservatives', 'unit_of_measurement' => 'kg', 'minimum_stock' => 5, 'cost_per_unit' => 350.00, 'supplier' => 'Preserve It Corp.', 'location' => 'Warehouse A - Shelf 4', 'description' => 'Food preservative', 'beginning_inventory' => 50, 'in_items' => 0, 'is_active' => true],
            ['name' => 'Cocoa Powder', 'sku' => 'ING-COC-001', 'category_slug' => 'powders', 'unit_of_measurement' => 'kg', 'minimum_stock' => 20, 'cost_per_unit' => 300.00, 'supplier' => 'Cocoa Traders', 'location' => 'Warehouse A - Shelf 5', 'description' => 'Dutch process cocoa powder', 'beginning_inventory' => 200, 'in_items' => 0, 'is_active' => true],
            ['name' => 'Coffee Extract', 'sku' => 'ING-CFE-001', 'category_slug' => 'powders', 'unit_of_measurement' => 'L', 'minimum_stock' => 30, 'cost_per_unit' => 180.00, 'supplier' => 'Coffee Bean Co.', 'location' => 'Warehouse A - Shelf 6', 'description' => 'Concentrated coffee extract', 'beginning_inventory' => 100, 'in_items' => 0, 'is_active' => true],
            ['name' => 'Milk Powder', 'sku' => 'ING-MLK-001', 'category_slug' => 'dairy', 'unit_of_measurement' => 'kg', 'minimum_stock' => 25, 'cost_per_unit' => 150.00, 'supplier' => 'Dairy Fresh', 'location' => 'Cold Storage - Shelf 1', 'description' => 'Full cream milk powder', 'beginning_inventory' => 150, 'in_items' => 0, 'is_active' => true],
            ['name' => 'Creamer', 'sku' => 'ING-CRM-001', 'category_slug' => 'dairy', 'unit_of_measurement' => 'kg', 'minimum_stock' => 15, 'cost_per_unit' => 200.00, 'supplier' => 'Dairy Fresh', 'location' => 'Cold Storage - Shelf 2', 'description' => 'Non-dairy creamer', 'beginning_inventory' => 80, 'in_items' => 0, 'is_active' => true],
            ['name' => 'Tea Extract', 'sku' => 'ING-TEA-001', 'category_slug' => 'powders', 'unit_of_measurement' => 'L', 'minimum_stock' => 20, 'cost_per_unit' => 220.00, 'supplier' => 'Tea Masters', 'location' => 'Warehouse A - Shelf 7', 'description' => 'Black tea concentrate', 'beginning_inventory' => 100, 'in_items' => 0, 'is_active' => true],
        ];

        foreach ($ingredients as $row) {
            $slug = $row['category_slug'];
            unset($row['category_slug']);
            $row['category_id'] = $categoryIds[$slug] ?? null;

            Ingredient::firstOrCreate(['sku' => $row['sku']], $row);
        }

        $batches = [
            ['ingredient_sku' => 'ING-ORC-001', 'batch_number' => 'ORC-2024-001', 'quantity' => 500, 'remaining_quantity' => 500, 'cost_per_unit' => 250.00, 'received_date' => '2024-01-15', 'expiry_date' => '2024-12-15', 'supplier' => 'Citrus Suppliers Inc.', 'status' => 'available'],
            ['ingredient_sku' => 'ING-SUG-001', 'batch_number' => 'SUG-2024-001', 'quantity' => 1000, 'remaining_quantity' => 1000, 'cost_per_unit' => 45.00, 'received_date' => '2024-01-10', 'expiry_date' => '2025-01-10', 'supplier' => 'Sugar Corp.', 'status' => 'available'],
            ['ingredient_sku' => 'ING-CIT-001', 'batch_number' => 'CIT-2024-001', 'quantity' => 100, 'remaining_quantity' => 100, 'cost_per_unit' => 120.00, 'received_date' => '2024-01-05', 'expiry_date' => '2025-01-05', 'supplier' => 'Acid Chemicals Co.', 'status' => 'available'],
            ['ingredient_sku' => 'ING-WTR-001', 'batch_number' => 'WTR-2024-001', 'quantity' => 10000, 'remaining_quantity' => 10000, 'cost_per_unit' => 5.00, 'received_date' => '2024-01-01', 'expiry_date' => null, 'supplier' => 'Water Systems Inc.', 'status' => 'available'],
            ['ingredient_sku' => 'ING-PRV-001', 'batch_number' => 'PRV-2024-001', 'quantity' => 50, 'remaining_quantity' => 50, 'cost_per_unit' => 350.00, 'received_date' => '2024-01-20', 'expiry_date' => '2025-01-20', 'supplier' => 'Preserve It Corp.', 'status' => 'available'],
            ['ingredient_sku' => 'ING-COC-001', 'batch_number' => 'COC-2024-001', 'quantity' => 200, 'remaining_quantity' => 200, 'cost_per_unit' => 300.00, 'received_date' => '2024-02-01', 'expiry_date' => '2025-06-01', 'supplier' => 'Cocoa Traders', 'status' => 'available'],
            ['ingredient_sku' => 'ING-CFE-001', 'batch_number' => 'CFE-2024-001', 'quantity' => 100, 'remaining_quantity' => 100, 'cost_per_unit' => 180.00, 'received_date' => '2024-02-05', 'expiry_date' => '2025-02-05', 'supplier' => 'Coffee Bean Co.', 'status' => 'available'],
            ['ingredient_sku' => 'ING-MLK-001', 'batch_number' => 'MLK-2024-001', 'quantity' => 150, 'remaining_quantity' => 150, 'cost_per_unit' => 150.00, 'received_date' => '2024-02-10', 'expiry_date' => '2025-08-10', 'supplier' => 'Dairy Fresh', 'status' => 'available'],
            ['ingredient_sku' => 'ING-CRM-001', 'batch_number' => 'CRM-2024-001', 'quantity' => 80, 'remaining_quantity' => 80, 'cost_per_unit' => 200.00, 'received_date' => '2024-02-12', 'expiry_date' => '2025-08-12', 'supplier' => 'Dairy Fresh', 'status' => 'available'],
            ['ingredient_sku' => 'ING-TEA-001', 'batch_number' => 'TEA-2024-001', 'quantity' => 100, 'remaining_quantity' => 100, 'cost_per_unit' => 220.00, 'received_date' => '2024-02-15', 'expiry_date' => '2025-02-15', 'supplier' => 'Tea Masters', 'status' => 'available'],
        ];

        foreach ($batches as $batch) {
            $ingredient = Ingredient::where('sku', $batch['ingredient_sku'])->first();
            if (!$ingredient) {
                continue;
            }

            unset($batch['ingredient_sku']);
            $batch['ingredient_id'] = $ingredient->id;

            IngredientBatch::firstOrCreate(['batch_number' => $batch['batch_number']], $batch);
        }

        // Product ↔ ingredient links (ingredient_product pivot + batch_rules)
        $productIngredients = [
            'JUC-001' => [
                ['sku' => 'ING-ORC-001', 'measurement' => '0.15 kg', 'batch_limit' => 1, 'rules' => [
                    ['batch_limit' => 1, 'measurement' => '0.15 kg Orange Concentrate'],
                    ['batch_limit' => 5, 'measurement' => '0.75 kg Orange Concentrate'],
                ]],
                ['sku' => 'ING-SUG-001', 'measurement' => '0.10 kg', 'batch_limit' => 1],
                ['sku' => 'ING-CIT-001', 'measurement' => '0.002 kg', 'batch_limit' => 1],
                ['sku' => 'ING-WTR-001', 'measurement' => '0.85 L', 'batch_limit' => 1],
                ['sku' => 'ING-PRV-001', 'measurement' => '0.001 kg', 'batch_limit' => 1],
            ],
            'CHO-001' => [
                ['sku' => 'ING-COC-001', 'measurement' => '0.08 kg', 'batch_limit' => 1],
                ['sku' => 'ING-SUG-001', 'measurement' => '0.12 kg', 'batch_limit' => 1],
                ['sku' => 'ING-MLK-001', 'measurement' => '0.05 kg', 'batch_limit' => 1],
                ['sku' => 'ING-WTR-001', 'measurement' => '0.80 L', 'batch_limit' => 1],
            ],
            'COF-001' => [
                ['sku' => 'ING-CFE-001', 'measurement' => '0.10 L', 'batch_limit' => 1],
                ['sku' => 'ING-SUG-001', 'measurement' => '0.08 kg', 'batch_limit' => 1],
                ['sku' => 'ING-CRM-001', 'measurement' => '0.03 kg', 'batch_limit' => 1],
                ['sku' => 'ING-WTR-001', 'measurement' => '0.85 L', 'batch_limit' => 1],
            ],
            'MTE-001' => [
                ['sku' => 'ING-TEA-001', 'measurement' => '0.08 L', 'batch_limit' => 1],
                ['sku' => 'ING-MLK-001', 'measurement' => '0.04 kg', 'batch_limit' => 1],
                ['sku' => 'ING-SUG-001', 'measurement' => '0.10 kg', 'batch_limit' => 1],
                ['sku' => 'ING-WTR-001', 'measurement' => '0.80 L', 'batch_limit' => 1],
            ],
        ];

        foreach ($productIngredients as $productCode => $links) {
            $product = Product::where('code', $productCode)->first();
            if (!$product) {
                continue;
            }

            foreach ($links as $link) {
                $ingredient = Ingredient::where('sku', $link['sku'])->first();
                if (!$ingredient) {
                    continue;
                }

                $existing = DB::table('ingredient_product')
                    ->where('product_id', $product->id)
                    ->where('ingredient_id', $ingredient->id)
                    ->first();

                if ($existing) {
                    DB::table('ingredient_product')
                        ->where('id', $existing->id)
                        ->update([
                            'measurement' => $link['measurement'],
                            'batch_limit' => $link['batch_limit'] ?? null,
                            'updated_at' => now(),
                        ]);
                    $ingredientProductId = $existing->id;
                } else {
                    $ingredientProductId = DB::table('ingredient_product')->insertGetId([
                        'product_id' => $product->id,
                        'ingredient_id' => $ingredient->id,
                        'measurement' => $link['measurement'],
                        'batch_limit' => $link['batch_limit'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                if (!empty($link['rules'])) {
                    BatchRule::where('ingredient_product_id', $ingredientProductId)->delete();

                    foreach ($link['rules'] as $rule) {
                        BatchRule::create([
                            'ingredient_product_id' => $ingredientProductId,
                            'batch_limit' => $rule['batch_limit'],
                            'measurement' => $rule['measurement'],
                        ]);
                    }
                }
            }
        }

        $this->command->info('Ingredients, batches, and product links seeded successfully!');
    }
}
