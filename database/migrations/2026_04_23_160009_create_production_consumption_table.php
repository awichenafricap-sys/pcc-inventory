<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_consumption', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_schedule_id')->constrained()->onDelete('cascade')->comment('Foreign key to production_schedules');
            $table->foreignId('ingredient_batch_id')->constrained()->onDelete('restrict')->comment('Foreign key to ingredient_batches (kung saang batch kinuha)');
            $table->decimal('expected_quantity', 10, 2)->comment('Dapat na dami base sa recipe');
            $table->decimal('actual_quantity', 10, 2)->comment('Actual na nagamit');
            $table->decimal('waste_quantity', 10, 2)->default(0)->comment('Nasayang na dami');
            $table->text('notes')->nullable()->comment('Notes about usage');
            $table->timestamps();

            $table->index('production_schedule_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_consumption');
    }
};
