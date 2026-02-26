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
        Schema::table('produce', function (Blueprint $table) {
            if (! Schema::hasColumn('produce', 'product_id')) {
                $table->foreignId('product_id')->nullable()->after('id')->constrained('products')->nullOnDelete();
            }

            if (! Schema::hasColumn('produce', 'quantity')) {
                $table->integer('quantity')->default(1)->after('category');
            }

            if (! Schema::hasColumn('produce', 'produced_at')) {
                $table->date('produced_at')->nullable()->after('quantity');
            }

            if (! Schema::hasColumn('produce', 'notes')) {
                $table->text('notes')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produce', function (Blueprint $table) {
            if (Schema::hasColumn('produce', 'product_id')) {
                $table->dropConstrainedForeignId('product_id');
            }

            if (Schema::hasColumn('produce', 'quantity')) {
                $table->dropColumn('quantity');
            }

            if (Schema::hasColumn('produce', 'produced_at')) {
                $table->dropColumn('produced_at');
            }

            if (Schema::hasColumn('produce', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
