<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_flavors', function (Blueprint $table) {
            // Add new columns
            $table->text('ingredients_text')->nullable()->after('measurement')->comment('Text description of ingredients');
            $table->boolean('is_active')->default(true)->after('batch')->comment('Is this flavor still available?');

            // Drop old columns (sizes/ingredients now in product_sizes/product_recipe_items)
            $table->dropColumn([
                'sizes',
                'ingredients',
                'qty_200ml',
                'qty_500ml',
                'qty_1000ml',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('product_flavors', function (Blueprint $table) {
            $table->dropColumn(['ingredients_text', 'is_active']);

            $table->string('sizes')->after('measurement');
            $table->text('ingredients')->after('sizes');
            $table->integer('qty_200ml')->default(0)->after('batch');
            $table->integer('qty_500ml')->default(0)->after('qty_200ml');
            $table->integer('qty_1000ml')->default(0)->after('qty_500ml');
        });
    }
};
