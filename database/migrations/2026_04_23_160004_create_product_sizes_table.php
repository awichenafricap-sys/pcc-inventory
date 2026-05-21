<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_flavor_id')->constrained()->onDelete('cascade')->comment('Foreign key to product_flavors');
            $table->integer('size_ml')->comment('Size in milliliters (200, 500, 1000, 1500)');
            $table->decimal('price', 10, 2)->nullable()->comment('Selling price for this size');
            $table->string('sku')->nullable()->comment('Size-specific SKU (JUC-OR-500ML)');
            $table->boolean('is_active')->default(true)->comment('Is this size available?');
            $table->timestamps();

            $table->unique(['product_flavor_id', 'size_ml'], 'unique_flavor_size');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_sizes');
    }
};
