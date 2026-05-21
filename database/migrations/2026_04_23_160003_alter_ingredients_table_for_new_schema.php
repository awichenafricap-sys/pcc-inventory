<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add is_active if not exists
        if (!Schema::hasColumn('ingredients', 'is_active')) {
            Schema::table('ingredients', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('description')->comment('Is this ingredient still used?');
            });
        }

        // Drop old FK on category_id (use raw SQL to find constraint name)
        $fk = DB::selectOne("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
              AND TABLE_NAME = 'ingredients' 
              AND COLUMN_NAME = 'category_id' 
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        if ($fk) {
            DB::statement("ALTER TABLE `ingredients` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }

        // Make category_id nullable + add new FK with SET NULL
        Schema::table('ingredients', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->change();
        });
        Schema::table('ingredients', function (Blueprint $table) {
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('set null');
        });

        // Change sku to nullable
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropUnique(['sku']);
            $table->string('sku')->unique()->nullable()->change();
        });

        // Drop old columns
        $cols = Schema::getColumnListing('ingredients');
        $dropCols = ['current_stock', 'beginning_inventory', 'received_date', 'received_quantity', 'released_date', 'released_quantity', 'ending_inventory', 'expiry_date', 'status', 'remarks'];
        $existing = array_intersect($dropCols, $cols);
        if (!empty($existing)) {
            Schema::table('ingredients', function (Blueprint $table) use ($existing) {
                $table->dropColumn(array_values($existing));
            });
        }
    }

    public function down(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn('is_active');

            // Restore category_id FK to restrict
            $table->dropForeign(['category_id']);
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('restrict');

            // Restore sku to NOT NULL
            $table->dropUnique(['sku']);
            $table->string('sku')->unique()->nullable(false)->change();

            // Restore old columns
            $table->decimal('current_stock', 10, 2)->default(0)->after('unit_of_measurement');
            $table->decimal('beginning_inventory', 10, 2)->default(0)->after('current_stock');
            $table->date('received_date')->nullable()->after('beginning_inventory');
            $table->decimal('received_quantity', 10, 2)->default(0)->after('received_date');
            $table->date('released_date')->nullable()->after('received_quantity');
            $table->decimal('released_quantity', 10, 2)->default(0)->after('released_date');
            $table->decimal('ending_inventory', 10, 2)->default(0)->after('released_quantity');
            $table->date('expiry_date')->nullable()->after('location');
            $table->enum('status', ['in_stock', 'low_stock', 'out_of_stock'])->default('in_stock')->after('expiry_date');
            $table->text('remarks')->nullable()->after('description');
        });
    }
};
