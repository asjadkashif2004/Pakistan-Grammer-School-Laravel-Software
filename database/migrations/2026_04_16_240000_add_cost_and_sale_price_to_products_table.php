<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('cost_price', 10, 2)->default(0)->after('description');
            $table->decimal('sale_price', 10, 2)->default(0)->after('cost_price');
        });

        // Backfill: treat existing `unit_price` as the current sale price.
        DB::table('products')->update([
            'sale_price' => DB::raw('COALESCE(unit_price, 0)'),
            'cost_price' => DB::raw('0'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['cost_price', 'sale_price']);
        });
    }
};

