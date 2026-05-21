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
            $table->decimal('custom_val1', 10, 2)->nullable()->after('custom_op');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('column_configs', function (Blueprint $table) {
            $table->dropColumn('custom_val1');
        });
    }
};
