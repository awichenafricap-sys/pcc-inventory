<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_recipe_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_flavor_id')->constrained()->onDelete('cascade')->comment('Foreign key to product_flavors');
            $table->foreignId('ingredient_id')->constrained()->onDelete('restrict')->comment('Foreign key to ingredients');
            $table->decimal('quantity_required', 10, 2)->comment('Dami ng ingredient per 1 unit of product');
            $table->string('unit_of_measurement')->comment('Unit for this ingredient in recipe');
            $table->decimal('waste_percentage', 5, 2)->default(0)->comment('Expected waste percentage (e.g., 5% spoilage)');
            $table->timestamps();

            $table->unique(['product_flavor_id', 'ingredient_id'], 'unique_recipe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_recipe_items');
    }
};
