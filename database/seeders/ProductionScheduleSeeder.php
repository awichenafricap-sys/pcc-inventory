<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductionSchedule;
use Illuminate\Database\Seeder;

class ProductionScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // Get all products
        $choco = Product::where('code', 'CHO-001')->first();
        $coffee = Product::where('code', 'COF-001')->first();
        $lacto = Product::where('code', 'LAC-001')->first();
        $yogurt = Product::where('code', 'YOG-001')->first();
        $milkTea = Product::where('code', 'MTE-001')->first();

        if (!$choco || !$coffee || !$lacto || !$yogurt || !$milkTea) {
            $this->command->warn('Please run ProductSeeder first.');
            return;
        }

        // Production schedules for 1 week (Monday to Sunday)
        $schedules = [
            // Monday (2024-01-15)
            '2024-01-15' => [
                [$choco->id, 5],
                [$coffee->id, 2],
                [$lacto->id, 4],
                [$yogurt->id, 0],
                [$milkTea->id, 3],
            ],
            // Tuesday (2024-01-16)
            '2024-01-16' => [
                [$choco->id, 3],
                [$coffee->id, 5],
                [$lacto->id, 2],
                [$yogurt->id, 1],
                [$milkTea->id, 4],
            ],
            // Wednesday (2024-01-17)
            '2024-01-17' => [
                [$choco->id, 0],
                [$coffee->id, 1],
                [$lacto->id, 3],
                [$yogurt->id, 2],
                [$milkTea->id, 2],
            ],
            // Thursday (2024-01-18)
            '2024-01-18' => [
                [$choco->id, 4],
                [$coffee->id, 3],
                [$lacto->id, 1],
                [$yogurt->id, 5],
                [$milkTea->id, 0],
            ],
            // Friday (2024-01-19)
            '2024-01-19' => [
                [$choco->id, 2],
                [$coffee->id, 4],
                [$lacto->id, 5],
                [$yogurt->id, 3],
                [$milkTea->id, 1],
            ],
            // Saturday (2024-01-20)
            '2024-01-20' => [
                [$choco->id, 6],
                [$coffee->id, 2],
                [$lacto->id, 0],
                [$yogurt->id, 4],
                [$milkTea->id, 5],
            ],
            // Sunday (2024-01-21)
            '2024-01-21' => [
                [$choco->id, 1],
                [$coffee->id, 0],
                [$lacto->id, 2],
                [$yogurt->id, 3],
                [$milkTea->id, 4],
            ],
        ];

        foreach ($schedules as $date => $products) {
            foreach ($products as [$productId, $batchQuantity]) {
                ProductionSchedule::updateOrCreate(
                    [
                        'product_id' => $productId,
                        'production_date' => $date,
                    ],
                    [
                        'batch_quantity' => $batchQuantity,
                    ]
                );
            }
        }
    }
}
