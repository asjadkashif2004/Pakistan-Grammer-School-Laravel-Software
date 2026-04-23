<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_fees', function (Blueprint $table) {
            if (! Schema::hasColumn('class_fees', 'monthly_fee')) {
                $table->decimal('monthly_fee', 10, 2)->default(0)->after('tuition_fee');
            }

            if (! Schema::hasColumn('class_fees', 'admission_fee')) {
                $table->decimal('admission_fee', 10, 2)->default(0)->after('monthly_fee');
            }
        });

        if (Schema::hasColumn('class_fees', 'monthly_fee') && Schema::hasColumn('class_fees', 'tuition_fee')) {
            DB::table('class_fees')
                ->where('monthly_fee', 0)
                ->update([
                    'monthly_fee' => DB::raw('tuition_fee'),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('class_fees', function (Blueprint $table) {
            if (Schema::hasColumn('class_fees', 'admission_fee')) {
                $table->dropColumn('admission_fee');
            }
            if (Schema::hasColumn('class_fees', 'monthly_fee')) {
                $table->dropColumn('monthly_fee');
            }
        });
    }
};

