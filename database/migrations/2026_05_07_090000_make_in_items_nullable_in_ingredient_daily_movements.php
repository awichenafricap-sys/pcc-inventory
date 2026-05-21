<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ingredient_daily_movements', function (Blueprint $table) {
            $table->decimal('in_items', 10, 2)->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('ingredient_daily_movements', function (Blueprint $table) {
            $table->decimal('in_items', 10, 2)->default(0)->nullable(false)->change();
        });
    }
};
