<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE students MODIFY father_cnic VARCHAR(15) NULL');
            DB::statement('ALTER TABLE students MODIFY contact_number VARCHAR(12) NULL');
            DB::statement('ALTER TABLE staff_members MODIFY cnic VARCHAR(15) NULL');
            DB::statement('ALTER TABLE staff_members MODIFY contact_number VARCHAR(12) NULL');
            DB::statement('ALTER TABLE staff_members MODIFY phone VARCHAR(12) NULL');
            DB::statement('ALTER TABLE sales_invoices MODIFY customer_contact VARCHAR(12) NULL');
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE students MODIFY father_cnic VARCHAR(25) NULL');
            DB::statement('ALTER TABLE students MODIFY contact_number VARCHAR(30) NULL');
            DB::statement('ALTER TABLE staff_members MODIFY cnic VARCHAR(15) NULL');
            DB::statement('ALTER TABLE staff_members MODIFY contact_number VARCHAR(11) NULL');
            DB::statement('ALTER TABLE staff_members MODIFY phone VARCHAR(40) NULL');
            DB::statement('ALTER TABLE sales_invoices MODIFY customer_contact VARCHAR(40) NULL');
        }
    }
};
