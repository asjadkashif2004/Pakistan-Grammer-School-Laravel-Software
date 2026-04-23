<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('office_bform_file_path')->nullable()->after('office_bform_submitted');
            $table->string('office_father_cnic_file_path')->nullable()->after('office_father_cnic_submitted');
            $table->string('office_result_cards_file_path')->nullable()->after('office_result_cards_submitted');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'office_father_cnic_file_path',
                'office_bform_file_path',
                'office_result_cards_file_path',
            ]);
        });
    }
};
