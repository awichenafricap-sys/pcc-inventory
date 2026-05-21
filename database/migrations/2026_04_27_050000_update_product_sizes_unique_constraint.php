<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop FK on product_sizes that depends on the unique_flavor_size index
        Schema::table('product_sizes', function (Blueprint $table) {
            $table->dropForeign('product_sizes_product_flavor_id_foreign');
        });

        // Now safe to change the unique constraint
        Schema::table('product_sizes', function (Blueprint $table) {
            $table->dropUnique('unique_flavor_size');
            $table->unique(['product_flavor_id', 'column_config_id'], 'unique_flavor_column');
        });

        // Re-add FK
        Schema::table('product_sizes', function (Blueprint $table) {
            $table->foreign('product_flavor_id')->references('id')->on('product_flavors')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('product_sizes', function (Blueprint $table) {
            $table->dropForeign('product_sizes_product_flavor_id_foreign');
        });

        Schema::table('product_sizes', function (Blueprint $table) {
            $table->dropUnique('unique_flavor_column');
            $table->unique(['product_flavor_id', 'size_ml'], 'unique_flavor_size');
        });

        Schema::table('product_sizes', function (Blueprint $table) {
            $table->foreign('product_flavor_id')->references('id')->on('product_flavors')->onDelete('cascade');
        });
    }
};
