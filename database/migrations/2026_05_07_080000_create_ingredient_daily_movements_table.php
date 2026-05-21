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
        Schema::create('ingredient_daily_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->date('movement_date');
            $table->decimal('in_items', 10, 2)->default(0);
            $table->decimal('total_out', 10, 2)->default(0);
            $table->decimal('ending', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['ingredient_id', 'movement_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredient_daily_movements');
    }
};
