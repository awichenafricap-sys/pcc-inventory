<?php

namespace Database\Seeders;

use App\Models\ColumnConfig;
use Illuminate\Database\Seeder;

class ColumnConfigSeeder extends Seeder
{
    public function run(): void
    {
        $columns = [
            // Bottle columns
            ['type' => 'Bottle', 'column_name' => 'batch', 'column_label' => 'Batch', 'sort_order' => 1, 'is_active' => true, 'divisor_type' => 'none'],
            ['type' => 'Bottle', 'column_name' => '200ml', 'column_label' => '200ml', 'sort_order' => 2, 'is_active' => true, 'divisor_type' => 'auto'],
            ['type' => 'Bottle', 'column_name' => '500ml', 'column_label' => '500ml', 'sort_order' => 3, 'is_active' => true, 'divisor_type' => 'auto'],
            ['type' => 'Bottle', 'column_name' => '1000ml', 'column_label' => '1000ml', 'sort_order' => 4, 'is_active' => true, 'divisor_type' => 'auto'],

            // Sachet columns
            ['type' => 'Sachet', 'column_name' => 'batch', 'column_label' => 'Batch', 'sort_order' => 1, 'is_active' => true, 'divisor_type' => 'none'],
            ['type' => 'Sachet', 'column_name' => '50ml', 'column_label' => '50ml', 'sort_order' => 2, 'is_active' => true, 'divisor_type' => 'auto'],
            ['type' => 'Sachet', 'column_name' => '100ml', 'column_label' => '100ml', 'sort_order' => 3, 'is_active' => true, 'divisor_type' => 'auto'],

            // Cup columns
            ['type' => 'Cup', 'column_name' => 'batch', 'column_label' => 'Batch', 'sort_order' => 1, 'is_active' => true, 'divisor_type' => 'none'],
            ['type' => 'Cup', 'column_name' => '150ml', 'column_label' => '150ml', 'sort_order' => 2, 'is_active' => true, 'divisor_type' => 'auto'],
            ['type' => 'Cup', 'column_name' => '250ml', 'column_label' => '250ml', 'sort_order' => 3, 'is_active' => true, 'divisor_type' => 'auto'],

            // Yogurt columns
            ['type' => 'Yogurt', 'column_name' => 'batch', 'column_label' => 'Batch', 'sort_order' => 1, 'is_active' => true, 'divisor_type' => 'none'],
            ['type' => 'Yogurt', 'column_name' => '120ml', 'column_label' => '120ml', 'sort_order' => 2, 'is_active' => true, 'divisor_type' => 'auto'],
            ['type' => 'Yogurt', 'column_name' => '150ml', 'column_label' => '150ml', 'sort_order' => 3, 'is_active' => true, 'divisor_type' => 'auto'],
            ['type' => 'Yogurt', 'column_name' => '200ml', 'column_label' => '200ml', 'sort_order' => 4, 'is_active' => true, 'divisor_type' => 'auto'],
        ];

        foreach ($columns as $column) {
            ColumnConfig::firstOrCreate(
                ['type' => $column['type'], 'column_name' => $column['column_name']],
                $column
            );
        }
    }
}
