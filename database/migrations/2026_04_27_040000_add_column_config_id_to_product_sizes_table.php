<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_sizes', function (Blueprint $table) {
            $table->foreignId('column_config_id')->nullable()->after('product_flavor_id')->constrained('column_configs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('product_sizes', function (Blueprint $table) {
            $table->dropForeign(['column_config_id']);
            $table->dropColumn('column_config_id');
        });
    }
};
