<?php

namespace Database\Seeders;

use App\Models\ColumnConfig;
use App\Models\Product;
use App\Models\ProductFlavor;
use App\Models\ProductSize;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            [
                'code' => 'JUC-001',
                'name' => 'Orange Juice',
                'category' => 'Juice',
                'type' => 'Bottle',
                'unit' => 'Bottles',
                'description' => 'Freshly squeezed orange juice',
                'is_active' => true,
                'flavors' => [
                    [
                        'flavor_name' => 'Orange',
                        'ingredients_text' => 'Orange concentrate, Sugar, Citric acid, Water, Preservative',
                        'sizes' => [
                            ['column' => '200ml', 'price' => 25.00, 'sku' => 'JUC-OR-200ML'],
                            ['column' => '500ml', 'price' => 55.00, 'sku' => 'JUC-OR-500ML'],
                            ['column' => '1000ml', 'price' => 99.00, 'sku' => 'JUC-OR-1000ML'],
                        ],
                    ],
                    [
                        'flavor_name' => 'Orange with Pulp',
                        'ingredients_text' => 'Orange concentrate with pulp, Sugar, Citric acid, Water, Preservative',
                        'sizes' => [
                            ['column' => '500ml', 'price' => 60.00, 'sku' => 'JUC-ORP-500ML'],
                        ],
                    ],
                ],
            ],
            [
                'code' => 'JUC-002',
                'name' => 'Apple Juice',
                'category' => 'Juice',
                'type' => 'Bottle',
                'unit' => 'Bottles',
                'description' => 'Fresh apple juice',
                'is_active' => true,
                'flavors' => [
                    [
                        'flavor_name' => 'Apple',
                        'ingredients_text' => 'Apple concentrate, Sugar, Citric acid, Water, Preservative',
                        'sizes' => [
                            ['column' => '500ml', 'price' => 55.00, 'sku' => 'JUC-AP-500ML'],
                        ],
                    ],
                ],
            ],
            [
                'code' => 'JUC-003',
                'name' => 'Mango Juice',
                'category' => 'Juice',
                'type' => 'Bottle',
                'unit' => 'Bottles',
                'description' => 'Sweet mango juice',
                'is_active' => true,
                'flavors' => [
                    [
                        'flavor_name' => 'Mango',
                        'ingredients_text' => 'Mango concentrate, Sugar, Citric acid, Water, Preservative',
                        'sizes' => [
                            ['column' => '500ml', 'price' => 65.00, 'sku' => 'JUC-MG-500ML'],
                        ],
                    ],
                ],
            ],
            [
                'code' => 'CHO-001',
                'name' => 'Choco',
                'category' => 'Liquid',
                'type' => 'Bottle',
                'unit' => 'Bottles',
                'description' => 'Chocolate drink',
                'is_active' => true,
                'flavors' => [
                    [
                        'flavor_name' => 'Classic',
                        'ingredients_text' => 'Cocoa powder, Sugar, Milk, Water',
                        'sizes' => [
                            ['column' => '200ml', 'price' => 20.00, 'sku' => 'CHO-CL-200ML'],
                            ['column' => '500ml', 'price' => 45.00, 'sku' => 'CHO-CL-500ML'],
                        ],
                    ],
                ],
            ],
            [
                'code' => 'COF-001',
                'name' => 'Coffee',
                'category' => 'Liquid',
                'type' => 'Bottle',
                'unit' => 'Bottles',
                'description' => 'Ready-to-drink coffee',
                'is_active' => true,
                'flavors' => [
                    [
                        'flavor_name' => 'Original',
                        'ingredients_text' => 'Coffee extract, Sugar, Creamer, Water',
                        'sizes' => [
                            ['column' => '200ml', 'price' => 35.00, 'sku' => 'COF-OR-200ML'],
                        ],
                    ],
                ],
            ],
            [
                'code' => 'LAC-001',
                'name' => 'Lacto',
                'category' => 'Liquid',
                'type' => 'Bottle',
                'unit' => 'Bottles',
                'description' => 'Lacto drink',
                'is_active' => true,
                'flavors' => [
                    [
                        'flavor_name' => 'Original',
                        'ingredients_text' => 'Lacto base, Sugar, Water',
                        'sizes' => [
                            ['column' => '200ml', 'price' => 20.00, 'sku' => 'LAC-OR-200ML'],
                        ],
                    ],
                ],
            ],
            [
                'code' => 'YOG-001',
                'name' => 'Yogurt',
                'category' => 'Yogurt',
                'type' => 'Yogurt',
                'unit' => 'Bottles',
                'description' => 'Yogurt drink',
                'is_active' => true,
                'flavors' => [
                    [
                        'flavor_name' => 'Plain',
                        'ingredients_text' => 'Yogurt base, Sugar, Water',
                        'sizes' => [
                            ['column' => '120ml', 'price' => 15.00, 'sku' => 'YOG-PL-120ML'],
                            ['column' => '150ml', 'price' => 18.00, 'sku' => 'YOG-PL-150ML'],
                        ],
                    ],
                ],
            ],
            [
                'code' => 'MTE-001',
                'name' => 'Milk Tea',
                'category' => 'Liquid',
                'type' => 'Bottle',
                'unit' => 'Bottles',
                'description' => 'Milk tea drink',
                'is_active' => true,
                'flavors' => [
                    [
                        'flavor_name' => 'Classic',
                        'ingredients_text' => 'Tea extract, Milk, Sugar, Water',
                        'sizes' => [
                            ['column' => '500ml', 'price' => 40.00, 'sku' => 'MTE-CL-500ML'],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($catalog as $entry) {
            $flavors = $entry['flavors'];
            unset($entry['flavors']);

            $product = Product::updateOrCreate(['code' => $entry['code']], $entry);

            foreach ($flavors as $flavorData) {
                $sizes = $flavorData['sizes'];
                unset($flavorData['sizes']);

                $flavor = ProductFlavor::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'flavor_name' => $flavorData['flavor_name'],
                    ],
                    array_merge($flavorData, [
                        'batch' => 1,
                        'is_active' => true,
                    ])
                );

                foreach ($sizes as $sizeData) {
                    $columnConfig = ColumnConfig::where('type', $product->type)
                        ->where('column_name', $sizeData['column'])
                        ->first();

                    if (!$columnConfig) {
                        $this->command->warn("Column config not found: {$product->type}/{$sizeData['column']}");
                        continue;
                    }

                    $sizeMl = (int) preg_replace('/[^0-9]/', '', $sizeData['column']);

                    ProductSize::firstOrCreate(
                        [
                            'product_flavor_id' => $flavor->id,
                            'column_config_id' => $columnConfig->id,
                        ],
                        [
                            'size_ml' => $sizeMl,
                            'price' => $sizeData['price'],
                            'sku' => $sizeData['sku'],
                            'quantity' => 0,
                            'is_active' => true,
                        ]
                    );
                }
            }
        }

        $this->command->info('Products, flavors, and sizes seeded successfully!');
    }
}
