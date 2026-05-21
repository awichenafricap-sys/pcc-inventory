<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductFlavor;
use App\Models\ProductSize;
use App\Models\ProductionSchedule;
use Illuminate\Database\Seeder;

class ProductionScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $scheduleDefs = [
            [
                'product_code' => 'JUC-001',
                'flavor_name' => 'Orange',
                'column' => '500ml',
                'type' => 'Bottle',
                'dates' => [
                    ['offset' => 0, 'batch_quantity' => 5],
                    ['offset' => 1, 'batch_quantity' => 4],
                    ['offset' => 2, 'batch_quantity' => 4],
                ],
            ],
            [
                'product_code' => 'JUC-001',
                'flavor_name' => 'Orange',
                'column' => '1000ml',
                'type' => 'Bottle',
                'dates' => [
                    ['offset' => 1, 'batch_quantity' => 3],
                ],
            ],
            [
                'product_code' => 'CHO-001',
                'flavor_name' => 'Classic',
                'column' => '500ml',
                'type' => 'Bottle',
                'dates' => [
                    ['offset' => 0, 'batch_quantity' => 2],
                    ['offset' => 1, 'batch_quantity' => 5],
                ],
            ],
            [
                'product_code' => 'COF-001',
                'flavor_name' => 'Original',
                'column' => '200ml',
                'type' => 'Bottle',
                'dates' => [
                    ['offset' => 0, 'batch_quantity' => 4],
                    ['offset' => 1, 'batch_quantity' => 2],
                    ['offset' => 2, 'batch_quantity' => 1],
                ],
            ],
            [
                'product_code' => 'LAC-001',
                'flavor_name' => 'Original',
                'column' => '200ml',
                'type' => 'Bottle',
                'dates' => [
                    ['offset' => 0, 'batch_quantity' => 3],
                    ['offset' => 2, 'batch_quantity' => 3],
                ],
            ],
            [
                'product_code' => 'YOG-001',
                'flavor_name' => 'Plain',
                'column' => '120ml',
                'type' => 'Yogurt',
                'dates' => [
                    ['offset' => 1, 'batch_quantity' => 1],
                    ['offset' => 2, 'batch_quantity' => 2],
                ],
            ],
            [
                'product_code' => 'MTE-001',
                'flavor_name' => 'Classic',
                'column' => '500ml',
                'type' => 'Bottle',
                'dates' => [
                    ['offset' => 0, 'batch_quantity' => 3],
                    ['offset' => 1, 'batch_quantity' => 4],
                    ['offset' => 2, 'batch_quantity' => 2],
                ],
            ],
        ];

        $weekStart = now()->startOfWeek();

        foreach ($scheduleDefs as $def) {
            $product = Product::where('code', $def['product_code'])->first();
            if (!$product) {
                continue;
            }

            $flavor = ProductFlavor::where('product_id', $product->id)
                ->where('flavor_name', $def['flavor_name'])
                ->first();

            if (!$flavor) {
                continue;
            }

            $size = ProductSize::where('product_flavor_id', $flavor->id)
                ->whereHas('columnConfig', fn ($q) => $q->where('column_name', $def['column']))
                ->first();

            if (!$size) {
                $this->command->warn("Size not found: {$def['product_code']} / {$def['flavor_name']} / {$def['column']}");
                continue;
            }

            foreach ($def['dates'] as $entry) {
                $date = $weekStart->copy()->addDays($entry['offset'])->toDateString();

                ProductionSchedule::updateOrCreate(
                    [
                        'product_flavor_id' => $flavor->id,
                        'product_size_id' => $size->id,
                        'production_date' => $date,
                        'type' => $def['type'],
                    ],
                    [
                        'batch_quantity' => $entry['batch_quantity'],
                        'status' => 'planned',
                    ]
                );
            }
        }

        $this->command->info('Production schedules seeded successfully!');
    }
}
