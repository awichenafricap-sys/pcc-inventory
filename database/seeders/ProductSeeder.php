<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'code' => 'CHO-001',
                'name' => 'Choco',
                'category' => 'Liquid',
                'unit' => 'ml',
                'container_size_ml' => 1000,
            ],
            [
                'code' => 'COF-001',
                'name' => 'Coffee',
                'category' => 'Liquid',
                'unit' => 'ml',
                'container_size_ml' => 500,
            ],
            [
                'code' => 'LAC-001',
                'name' => 'Lacto',
                'category' => 'Liquid',
                'unit' => 'ml',
                'container_size_ml' => 200,
            ],
            [
                'code' => 'YOG-001',
                'name' => 'Yogurt',
                'category' => 'Liquid',
                'unit' => 'ml',
                'container_size_ml' => 100,
            ],
            [
                'code' => 'MTE-001',
                'name' => 'Milk Tea',
                'category' => 'Liquid',
                'unit' => 'ml',
                'container_size_ml' => 250,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['code' => $product['code']],
                $product
            );
        }
    }
}
