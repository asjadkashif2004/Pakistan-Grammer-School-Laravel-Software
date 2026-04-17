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
        Schema::table('staff_members', function (Blueprint $table) {
            $table->string('employee_code', 32)->nullable()->after('id');
            $table->string('phone', 40)->nullable()->after('name');
            $table->string('designation')->nullable()->after('role');
            $table->decimal('monthly_wage', 12, 2)->default(0)->after('designation');
            $table->date('joining_date')->nullable()->after('monthly_wage');
            $table->string('bank_name')->nullable()->after('joining_date');
            $table->string('account_title')->nullable()->after('bank_name');
            $table->string('account_number')->nullable()->after('account_title');
            $table->enum('online_wallet_type', ['easypaisa', 'jazzcash'])->nullable()->after('account_number');
            $table->string('online_wallet_number', 40)->nullable()->after('online_wallet_type');
            $table->unique('employee_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_members', function (Blueprint $table) {
            $table->dropUnique(['employee_code']);
            $table->dropColumn([
                'employee_code',
                'phone',
                'designation',
                'monthly_wage',
                'joining_date',
                'bank_name',
                'account_title',
                'account_number',
                'online_wallet_type',
                'online_wallet_number',
            ]);
        });
    }
};
