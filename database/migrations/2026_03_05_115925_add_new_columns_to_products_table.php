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
        Schema::table('products', function (Blueprint $table) {
            $table->integer('beginning')->default(0)->after('unit');
            $table->decimal('cost', 10, 2)->nullable()->after('current_stock');
            $table->decimal('credit', 10, 2)->nullable()->after('cost');
            $table->string('other')->nullable()->after('category');
            $table->integer('ending')->nullable()->after('reorder_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['beginning', 'cost', 'credit', 'other', 'ending']);
        });
    }
};
