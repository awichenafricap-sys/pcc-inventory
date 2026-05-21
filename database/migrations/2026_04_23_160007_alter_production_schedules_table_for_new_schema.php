<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop FK first (unique index depends on it)
        $fk = DB::selectOne("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
              AND TABLE_NAME = 'production_schedules' 
              AND COLUMN_NAME = 'product_id' 
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        if ($fk) {
            DB::statement("ALTER TABLE `production_schedules` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }

        // Drop unique constraint using raw SQL
        $unique = DB::selectOne("
            SELECT INDEX_NAME 
            FROM INFORMATION_SCHEMA.STATISTICS 
            WHERE TABLE_SCHEMA = DATABASE() 
              AND TABLE_NAME = 'production_schedules' 
              AND COLUMN_NAME = 'product_id' 
              AND NON_UNIQUE = 0
        ");
        if ($unique && $unique->INDEX_NAME !== 'PRIMARY') {
            DB::statement("ALTER TABLE `production_schedules` DROP INDEX `{$unique->INDEX_NAME}`");
        }

        Schema::table('production_schedules', function (Blueprint $table) {

            // Drop product_id column if it still exists
            if (Schema::hasColumn('production_schedules', 'product_id')) {
                $table->dropColumn('product_id');
            }

            // Add new FKs (if not already added)
            if (!Schema::hasColumn('production_schedules', 'product_flavor_id')) {
                $table->foreignId('product_flavor_id')->after('id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('production_schedules', 'product_size_id')) {
                $table->foreignId('product_size_id')->after('product_flavor_id')->constrained()->onDelete('cascade');
            }

            // Add new columns (if not already added)
            if (!Schema::hasColumn('production_schedules', 'status')) {
                $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned')->after('batch_quantity');
            }
            if (!Schema::hasColumn('production_schedules', 'actual_start_date')) {
                $table->dateTime('actual_start_date')->nullable()->after('status');
            }
            if (!Schema::hasColumn('production_schedules', 'actual_end_date')) {
                $table->dateTime('actual_end_date')->nullable()->after('actual_start_date');
            }
            if (!Schema::hasColumn('production_schedules', 'notes')) {
                $table->text('notes')->nullable()->after('actual_end_date');
            }

            // Change batch_quantity to not have default
            $table->integer('batch_quantity')->default(null)->change();

            // New indexes
            $table->index('production_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        // Old schema used product_id; rows with only flavor/size IDs cannot be rolled back safely.
        DB::table('production_schedules')->truncate();

        // Drop FKs safely by name
        $fks = DB::select("
            SELECT CONSTRAINT_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'production_schedules'
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        foreach ($fks as $fk) {
            DB::statement("ALTER TABLE `production_schedules` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }

        // Drop unique/indexes safely
        $indexes = DB::select("
            SELECT INDEX_NAME
            FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'production_schedules'
              AND NON_UNIQUE = 0
              AND INDEX_NAME != 'PRIMARY'
        ");
        foreach ($indexes as $idx) {
            DB::statement("ALTER TABLE `production_schedules` DROP INDEX `{$idx->INDEX_NAME}`");
        }

        Schema::table('production_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('production_schedules', 'status')) {
                $table->dropColumn(['status', 'actual_start_date', 'actual_end_date', 'notes']);
            }
            if (Schema::hasColumn('production_schedules', 'product_flavor_id')) {
                $table->dropColumn(['product_flavor_id', 'product_size_id']);
            }

            if (!Schema::hasColumn('production_schedules', 'product_id')) {
                $table->foreignId('product_id')->after('id')->constrained()->onDelete('cascade');
                $table->unique(['product_id', 'production_date']);
            }

            $table->integer('batch_quantity')->default(0)->change();
        });
    }
};
