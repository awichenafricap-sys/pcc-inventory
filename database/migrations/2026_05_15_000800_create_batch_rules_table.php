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
        Schema::create('batch_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ingredient_product_id');
            $table->foreign('ingredient_product_id')->references('id')->on('ingredient_product')->onDelete('cascade');
            $table->integer('batch_limit');
            $table->string('measurement');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_rules');
    }
};
