<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (! Schema::hasColumn('students', 'is_defaulter')) {
                $table->boolean('is_defaulter')->default(false)->after('status');
            }
        });

        Schema::table('fee_collections', function (Blueprint $table) {
            if (! Schema::hasColumn('fee_collections', 'rolled_into_fee_collection_id')) {
                $table->foreignId('rolled_into_fee_collection_id')
                    ->nullable()
                    ->after('voucher_generated_at')
                    ->constrained('fee_collections')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('fee_collections', function (Blueprint $table) {
            if (Schema::hasColumn('fee_collections', 'rolled_into_fee_collection_id')) {
                $table->dropConstrainedForeignId('rolled_into_fee_collection_id');
            }
        });

        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'is_defaulter')) {
                $table->dropColumn('is_defaulter');
            }
        });
    }
};
