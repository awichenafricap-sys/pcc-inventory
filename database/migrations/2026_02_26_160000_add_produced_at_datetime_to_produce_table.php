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
        if (Schema::hasTable('produce')) {
            Schema::table('produce', function (Blueprint $table) {
                if (! Schema::hasColumn('produce', 'produced_at_datetime')) {
                    $table->dateTime('produced_at_datetime')->nullable()->after('produced_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('produce')) {
            Schema::table('produce', function (Blueprint $table) {
                if (Schema::hasColumn('produce', 'produced_at_datetime')) {
                    $table->dropColumn('produced_at_datetime');
                }
            });
        }
    }
};
