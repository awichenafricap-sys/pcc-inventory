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
        Schema::create('column_configs', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // Bottle, Sachet, Cup
            $table->string('column_name'); // e.g., '200ml', '500ml', 'batch'
            $table->string('column_label'); // e.g., '200ml', 'Batch'
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['type', 'column_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('column_configs');
    }
};
