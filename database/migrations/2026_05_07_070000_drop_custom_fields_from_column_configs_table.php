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
            $table->dropColumn(['custom_op', 'custom_val1', 'custom_val2']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('column_configs', function (Blueprint $table) {
            $table->string('custom_op')->default('divide')->after('divisor_value');
            $table->decimal('custom_val1', 10, 2)->nullable()->after('custom_op');
            $table->decimal('custom_val2', 10, 2)->nullable()->after('custom_val1');
        });
    }
};
