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
        Schema::table('fee_collections', function (Blueprint $table) {
            $table->string('voucher_number', 32)->nullable()->after('id');
            $table->date('due_date')->nullable()->after('billing_month');
            $table->decimal('arrears', 10, 2)->default(0)->after('amount');
            $table->decimal('fine', 10, 2)->default(0)->after('arrears');
            $table->decimal('discount', 10, 2)->default(0)->after('fine');
            $table->text('notes')->nullable()->after('discount');

            $table->unique('voucher_number');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_collections', function (Blueprint $table) {
            $table->dropUnique(['voucher_number']);
            $table->dropIndex(['due_date']);
            $table->dropColumn(['voucher_number', 'due_date', 'arrears', 'fine', 'discount', 'notes']);
        });
    }
};
