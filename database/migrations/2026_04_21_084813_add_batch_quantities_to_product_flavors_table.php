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
        Schema::table('product_flavors', function (Blueprint $table) {
            $table->integer('batch')->default(0)->after('ingredients');
            $table->integer('qty_200ml')->default(0)->after('batch');
            $table->integer('qty_500ml')->default(0)->after('qty_200ml');
            $table->integer('qty_1000ml')->default(0)->after('qty_500ml');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_flavors', function (Blueprint $table) {
            $table->dropColumn(['batch', 'qty_200ml', 'qty_500ml', 'qty_1000ml']);
        });
    }
};
