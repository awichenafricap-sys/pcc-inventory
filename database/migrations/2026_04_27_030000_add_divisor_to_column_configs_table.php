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
        Schema::table('column_configs', function (Blueprint $table) {
            $table->string('divisor_type')->default('none')->after('is_active'); // 'none', 'auto', 'custom'
            $table->integer('divisor_value')->nullable()->after('divisor_type'); // only used when divisor_type = 'custom'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('column_configs', function (Blueprint $table) {
            $table->dropColumn(['divisor_type', 'divisor_value']);
        });
    }
};
