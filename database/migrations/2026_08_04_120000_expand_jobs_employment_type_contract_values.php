<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE jobs MODIFY COLUMN employment_type ENUM('full-time','part-time','contract','permanent-contract','temporary-contract','part-time-contract','daily-paid-contract') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE jobs SET employment_type = 'full-time' WHERE employment_type = 'permanent-contract'");
        DB::statement("UPDATE jobs SET employment_type = 'contract' WHERE employment_type = 'temporary-contract'");
        DB::statement("UPDATE jobs SET employment_type = 'part-time' WHERE employment_type = 'part-time-contract'");
        DB::statement("UPDATE jobs SET employment_type = 'contract' WHERE employment_type = 'daily-paid-contract'");

        DB::statement("ALTER TABLE jobs MODIFY COLUMN employment_type ENUM('full-time','part-time','contract') NOT NULL");
    }
};
