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
        // Drop foreign key constraint from flavor_layouts first
        Schema::table('flavor_layouts', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        Schema::dropIfExists('add_overalls');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('add_overalls', function (Blueprint $table) {
            $table->id();
            $table->string('sizes')->nullable();
            $table->string('product_name')->nullable();
            $table->string('flavor')->nullable();
            $table->text('ingredients')->nullable();
            $table->string('category')->nullable();
            $table->string('type')->nullable();
            $table->string('unit')->nullable();
            $table->timestamps();
        });
    }
};
