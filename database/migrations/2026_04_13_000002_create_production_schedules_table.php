<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->date('production_date');
            $table->integer('batch_quantity')->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'production_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_schedules');
    }
};
