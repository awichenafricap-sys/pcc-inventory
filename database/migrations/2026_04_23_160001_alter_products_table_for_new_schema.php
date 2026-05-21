<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('image');

            // Drop old columns (moved to ingredient_batches / production tracking)
            $table->dropColumn([
                'credit',
                'other',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_active');

            $table->decimal('credit', 10, 2)->nullable()->after('unit');
            $table->string('other')->nullable()->after('category');
        });
    }
};
