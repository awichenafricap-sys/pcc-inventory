<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->decimal('beginning_inventory', 10, 2)->default(0)->after('unit_of_measurement');
            $table->date('date_receive')->nullable()->after('beginning_inventory');
            $table->decimal('receive_items', 10, 2)->default(0)->after('date_receive');
            $table->decimal('actual_ending', 10, 2)->default(0)->after('receive_items');
            $table->decimal('released_used_items', 10, 2)->default(0)->after('actual_ending');
        });
    }

    public function down(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn([
                'beginning_inventory',
                'date_receive',
                'receive_items',
                'actual_ending',
                'released_used_items',
            ]);
        });
    }
};
