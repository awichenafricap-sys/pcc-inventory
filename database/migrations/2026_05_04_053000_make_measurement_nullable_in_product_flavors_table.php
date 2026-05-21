<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_flavors', function (Blueprint $table) {
            $table->string('measurement')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        DB::table('product_flavors')
            ->whereNull('measurement')
            ->update(['measurement' => '']);

        Schema::table('product_flavors', function (Blueprint $table) {
            $table->string('measurement')->nullable(false)->default('')->change();
        });
    }
};
