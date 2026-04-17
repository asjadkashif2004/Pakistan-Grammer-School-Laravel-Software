<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_transactions', function (Blueprint $table) {
            $table->enum('status', ['Paid', 'Unpaid'])->default('Unpaid')->after('transaction_type');
            $table->string('branch_code', 10)->nullable()->after('bank_name');
            $table->string('iban', 24)->nullable()->after('branch_code');
        });

        DB::table('payroll_transactions')
            ->where('transaction_type', 'wage')
            ->whereNotNull('paid_at')
            ->update(['status' => 'Paid']);
    }

    public function down(): void
    {
        Schema::table('payroll_transactions', function (Blueprint $table) {
            $table->dropColumn(['status', 'branch_code', 'iban']);
        });
    }
};

