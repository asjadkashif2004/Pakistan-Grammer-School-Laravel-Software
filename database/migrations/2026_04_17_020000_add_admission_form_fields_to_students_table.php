<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('form_number', 30)->nullable()->after('student_code');
            $table->string('father_occupation', 160)->nullable()->after('father_name');
            $table->string('guardian_name', 160)->nullable()->after('father_occupation');
            $table->string('previous_school', 255)->nullable()->after('guardian_name');
            $table->string('last_attended_class', 120)->nullable()->after('previous_school');
            $table->string('session_label', 50)->nullable()->after('last_attended_class');
            $table->string('emergency_contact_number', 12)->nullable()->after('contact_number');
            $table->string('student_photo_path')->nullable()->after('emergency_contact_number');
            $table->boolean('office_bform_submitted')->default(false)->after('student_photo_path');
            $table->boolean('office_father_cnic_submitted')->default(false)->after('office_bform_submitted');
            $table->boolean('office_result_cards_submitted')->default(false)->after('office_father_cnic_submitted');
            $table->boolean('office_consumable_fee_paid')->default(false)->after('office_result_cards_submitted');
            $table->boolean('office_photos_submitted')->default(false)->after('office_consumable_fee_paid');
            $table->boolean('office_admission_fee_paid')->default(false)->after('office_photos_submitted');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'form_number',
                'father_occupation',
                'guardian_name',
                'previous_school',
                'last_attended_class',
                'session_label',
                'emergency_contact_number',
                'student_photo_path',
                'office_bform_submitted',
                'office_father_cnic_submitted',
                'office_result_cards_submitted',
                'office_consumable_fee_paid',
                'office_photos_submitted',
                'office_admission_fee_paid',
            ]);
        });
    }
};
