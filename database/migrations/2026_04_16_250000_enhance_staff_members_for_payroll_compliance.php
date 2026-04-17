<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_members', function (Blueprint $table) {
            $table->string('cnic', 15)->nullable()->after('employee_code');
            $table->string('contact_number', 11)->nullable()->after('phone');
            $table->enum('payment_method', ['bank', 'wallet'])->default('bank')->after('online_wallet_number');
            $table->string('branch_code', 10)->nullable()->after('bank_name');
            $table->string('iban', 24)->nullable()->after('branch_code');
            $table->unique('cnic');
        });

        DB::table('staff_members')
            ->whereNotNull('phone')
            ->update(['contact_number' => DB::raw('LEFT(phone, 11)')]);
    }

    public function down(): void
    {
        Schema::table('staff_members', function (Blueprint $table) {
            $table->dropUnique(['cnic']);
            $table->dropColumn(['cnic', 'contact_number', 'payment_method', 'branch_code', 'iban']);
        });
    }
};

