<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (! Schema::hasColumn('students', 'sibling_discount_percentage')) {
                $table->decimal('sibling_discount_percentage', 5, 2)->default(0)->after('transport_fee');
            }
            if (! Schema::hasColumn('students', 'sibling_discount_amount')) {
                $table->decimal('sibling_discount_amount', 10, 2)->default(0)->after('sibling_discount_percentage');
            }
            if (! Schema::hasColumn('students', 'final_payable')) {
                $table->decimal('final_payable', 10, 2)->default(0)->after('sibling_discount_amount');
            }
            if (! Schema::hasColumn('students', 'has_sibling_discount')) {
                $table->boolean('has_sibling_discount')->default(false)->after('final_payable');
            }
        });

        Schema::table('fee_collections', function (Blueprint $table) {
            if (! Schema::hasColumn('fee_collections', 'gross_amount')) {
                $table->decimal('gross_amount', 10, 2)->default(0)->after('amount');
            }
            if (! Schema::hasColumn('fee_collections', 'sibling_discount_percentage')) {
                $table->decimal('sibling_discount_percentage', 5, 2)->default(0)->after('gross_amount');
            }
            if (! Schema::hasColumn('fee_collections', 'sibling_discount_amount')) {
                $table->decimal('sibling_discount_amount', 10, 2)->default(0)->after('sibling_discount_percentage');
            }
        });

        if (Schema::hasColumn('fee_collections', 'gross_amount') && Schema::hasColumn('fee_collections', 'amount')) {
            DB::table('fee_collections')->update([
                'gross_amount' => DB::raw('amount'),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            foreach (['has_sibling_discount', 'final_payable', 'sibling_discount_amount', 'sibling_discount_percentage'] as $column) {
                if (Schema::hasColumn('students', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('fee_collections', function (Blueprint $table) {
            foreach (['sibling_discount_amount', 'sibling_discount_percentage', 'gross_amount'] as $column) {
                if (Schema::hasColumn('fee_collections', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

