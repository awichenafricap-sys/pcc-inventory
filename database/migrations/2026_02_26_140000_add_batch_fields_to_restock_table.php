<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('restock')) {
            Schema::table('restock', function (Blueprint $table) {
                if (! Schema::hasColumn('restock', 'batch_code')) {
                    $table->string('batch_code')->nullable()->after('restock_date');
                }

                if (! Schema::hasColumn('restock', 'batch_date')) {
                    $table->date('batch_date')->nullable()->after('batch_code');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('restock')) {
            Schema::table('restock', function (Blueprint $table) {
                if (Schema::hasColumn('restock', 'batch_date')) {
                    $table->dropColumn('batch_date');
                }

                if (Schema::hasColumn('restock', 'batch_code')) {
                    $table->dropColumn('batch_code');
                }
            });
        }
    }
};
