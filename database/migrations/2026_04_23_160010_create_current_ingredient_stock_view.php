<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW `current_ingredient_stock` AS
            SELECT 
                i.id AS ingredient_id,
                i.name AS ingredient_name,
                i.sku,
                i.unit_of_measurement,
                i.minimum_stock,
                COALESCE(SUM(ib.remaining_quantity), 0) AS current_stock,
                CASE 
                    WHEN COALESCE(SUM(ib.remaining_quantity), 0) <= 0 THEN 'out_of_stock'
                    WHEN COALESCE(SUM(ib.remaining_quantity), 0) <= i.minimum_stock THEN 'low_stock'
                    ELSE 'in_stock'
                END AS status,
                COUNT(ib.id) AS active_batches_count,
                MIN(ib.expiry_date) AS nearest_expiry_date
            FROM ingredients i
            LEFT JOIN ingredient_batches ib ON i.id = ib.ingredient_id AND ib.status IN ('available', 'partial')
            GROUP BY i.id, i.name, i.sku, i.unit_of_measurement, i.minimum_stock
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS `current_ingredient_stock`");
    }
};
