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
        Schema::create('flavor_layouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('add_overalls')->onDelete('cascade');
            $table->json('columns')->nullable();
            $table->json('rows')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flavor_layouts');
    }
};
