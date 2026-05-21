<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('product_sizes', 'quantity')) {
            Schema::table('product_sizes', function (Blueprint $table) {
                $table->integer('quantity')->default(0)->after('sku')->comment('Quantity produced for this size');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('product_sizes', 'quantity')) {
            Schema::table('product_sizes', function (Blueprint $table) {
                $table->dropColumn('quantity');
            });
        }
    }
};
