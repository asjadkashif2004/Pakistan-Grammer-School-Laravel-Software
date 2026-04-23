<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_transactions', function (Blueprint $table) {
            $table->decimal('overtime_rate', 10, 2)->nullable()->after('hours');
            $table->decimal('base_salary', 12, 2)->nullable()->after('amount');
            $table->decimal('total_advance', 12, 2)->nullable()->after('base_salary');
            $table->decimal('total_overtime', 12, 2)->nullable()->after('total_advance');
            $table->decimal('absence_deduction', 12, 2)->nullable()->after('total_overtime');
            $table->decimal('absent_days', 5, 2)->nullable()->after('absence_deduction');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'overtime_rate',
                'base_salary',
                'total_advance',
                'total_overtime',
                'absence_deduction',
                'absent_days',
            ]);
        });
    }
};
