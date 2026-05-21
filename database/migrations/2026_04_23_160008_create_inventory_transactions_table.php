<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_batch_id')->constrained()->onDelete('cascade')->comment('Foreign key to ingredient_batches');
            $table->enum('transaction_type', ['received', 'released', 'adjusted', 'wasted', 'returned'])->comment('Type of transaction');
            $table->decimal('quantity', 10, 2)->comment('Quantity affected');
            $table->string('reference_type')->nullable()->comment('What caused this? (production_schedule, purchase_order, adjustment)');
            $table->unsignedBigInteger('reference_id')->nullable()->comment('ID of the reference (production_schedule.id, etc.)');
            $table->decimal('previous_balance', 10, 2)->comment('Balance before this transaction');
            $table->decimal('new_balance', 10, 2)->comment('Balance after this transaction');
            $table->text('notes')->nullable()->comment('Additional notes');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->comment('User who created this transaction');
            $table->timestamps();

            $table->index('transaction_type');
            $table->index(['reference_type', 'reference_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
