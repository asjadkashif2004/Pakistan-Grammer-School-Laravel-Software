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
        Schema::create('payroll_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_member_id')->constrained()->cascadeOnDelete();
            $table->enum('transaction_type', ['wage', 'advance', 'extra_hours']);
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('hours', 8, 2)->nullable();
            $table->date('transaction_month');
            $table->enum('payment_method', ['cash', 'bank', 'easypaisa', 'jazzcash'])->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_title')->nullable();
            $table->string('account_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['staff_member_id', 'transaction_month']);
            $table->index(['transaction_type', 'transaction_month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_transactions');
    }
};
