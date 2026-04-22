<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ColumnConfig;

class ColumnConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $columns = [
            // Bottle columns
            ['type' => 'Bottle', 'column_name' => 'batch', 'column_label' => 'Batch', 'sort_order' => 1],
            ['type' => 'Bottle', 'column_name' => '200ml', 'column_label' => '200ml', 'sort_order' => 2],
            ['type' => 'Bottle', 'column_name' => '500ml', 'column_label' => '500ml', 'sort_order' => 3],
            ['type' => 'Bottle', 'column_name' => '1000ml', 'column_label' => '1000ml', 'sort_order' => 4],

            // Sachet columns
            ['type' => 'Sachet', 'column_name' => 'batch', 'column_label' => 'Batch', 'sort_order' => 1],
            ['type' => 'Sachet', 'column_name' => '50ml', 'column_label' => '50ml', 'sort_order' => 2],
            ['type' => 'Sachet', 'column_name' => '100ml', 'column_label' => '100ml', 'sort_order' => 3],

            // Cup columns
            ['type' => 'Cup', 'column_name' => 'batch', 'column_label' => 'Batch', 'sort_order' => 1],
            ['type' => 'Cup', 'column_name' => '150ml', 'column_label' => '150ml', 'sort_order' => 2],
            ['type' => 'Cup', 'column_name' => '250ml', 'column_label' => '250ml', 'sort_order' => 3],
        ];

        foreach ($columns as $column) {
            ColumnConfig::create($column);
        }
    }
}
