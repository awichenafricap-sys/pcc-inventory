<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_daily_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->date('production_date');
            $table->string('type');
            $table->integer('total_batch')->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'production_date', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_daily_batches');
    }
};
