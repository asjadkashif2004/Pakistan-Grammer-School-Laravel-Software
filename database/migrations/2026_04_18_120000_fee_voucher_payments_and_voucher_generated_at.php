<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_voucher_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_collection_id')->constrained('fee_collections')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->date('paid_at');
            $table->string('notes', 500)->nullable();
            $table->timestamps();
            $table->index(['fee_collection_id', 'paid_at']);
        });

        Schema::table('fee_collections', function (Blueprint $table) {
            $table->timestamp('voucher_generated_at')->nullable()->after('notes');
        });

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE fee_collections MODIFY COLUMN status VARCHAR(20) NOT NULL DEFAULT 'Unpaid'");
            DB::table('fee_collections')->where('status', 'Pending')->update(['status' => 'Unpaid']);
        } elseif ($driver === 'sqlite') {
            Schema::table('fee_collections', function (Blueprint $table) {
                $table->string('status_label', 20)->default('Unpaid');
            });

            DB::table('fee_collections')->select('id', 'status')->orderBy('id')->get()->each(function ($row) {
                $mapped = match ($row->status) {
                    'Paid' => 'Paid',
                    'Overdue' => 'Overdue',
                    default => 'Unpaid',
                };
                DB::table('fee_collections')->where('id', $row->id)->update(['status_label' => $mapped]);
            });

            Schema::table('fee_collections', function (Blueprint $table) {
                $table->dropColumn('status');
            });

            Schema::table('fee_collections', function (Blueprint $table) {
                $table->renameColumn('status_label', 'status');
            });

            Schema::table('fee_collections', function (Blueprint $table) {
                $table->index(['status', 'billing_month']);
            });
        }

        DB::table('fee_collections')
            ->whereNotNull('voucher_number')
            ->whereNull('voucher_generated_at')
            ->update(['voucher_generated_at' => DB::raw('created_at')]);

        $paidRows = DB::table('fee_collections')->where('status', 'Paid')->get();
        foreach ($paidRows as $row) {
            if (DB::table('fee_voucher_payments')->where('fee_collection_id', $row->id)->exists()) {
                continue;
            }

            $paidDate = $row->paid_at
                ? Carbon::parse($row->paid_at)->toDateString()
                : now()->toDateString();

            DB::table('fee_voucher_payments')->insert([
                'fee_collection_id' => $row->id,
                'amount' => $row->amount,
                'paid_at' => $paidDate,
                'notes' => 'Legacy paid voucher balance',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_voucher_payments');

        if (Schema::hasColumn('fee_collections', 'voucher_generated_at')) {
            Schema::table('fee_collections', function (Blueprint $table) {
                $table->dropColumn('voucher_generated_at');
            });
        }
    }
};
