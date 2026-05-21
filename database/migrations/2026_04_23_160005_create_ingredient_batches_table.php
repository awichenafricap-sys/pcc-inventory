<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredient_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained()->onDelete('cascade')->comment('Foreign key to ingredients');
            $table->string('batch_number')->comment('Batch number from supplier (BATCH-2024-001)');
            $table->decimal('quantity', 10, 2)->comment('Original quantity received');
            $table->decimal('remaining_quantity', 10, 2)->comment('Quantity still available');
            $table->decimal('cost_per_unit', 10, 2)->comment('Cost for this specific batch');
            $table->date('received_date')->comment('Date received');
            $table->date('expiry_date')->nullable()->comment('Expiry date (if applicable)');
            $table->string('supplier')->nullable()->comment('Supplier for this batch');
            $table->enum('status', ['available', 'partial', 'expired', 'depleted'])->default('available')->comment('Current batch status');
            $table->timestamps();

            $table->index('expiry_date');
            $table->index('status');
            $table->index('batch_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredient_batches');
    }
};
