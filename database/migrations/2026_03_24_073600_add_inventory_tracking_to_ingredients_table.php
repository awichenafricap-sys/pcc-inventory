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
        Schema::table('ingredients', function (Blueprint $table) {
            $table->decimal('beginning_inventory', 10, 2)->default(0)->after('current_stock');
            $table->date('received_date')->nullable()->after('beginning_inventory');
            $table->decimal('received_quantity', 10, 2)->default(0)->after('received_date');
            $table->date('released_date')->nullable()->after('received_quantity');
            $table->decimal('released_quantity', 10, 2)->default(0)->after('released_date');
            $table->text('remarks')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn([
                'beginning_inventory',
                'received_date',
                'received_quantity',
                'released_date',
                'released_quantity',
                'remarks'
            ]);
        });
    }
};
