<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: expand enum to include both old and new values
        DB::statement("ALTER TABLE jobs MODIFY COLUMN employment_type ENUM('full-time','part-time','contract','permanent-contract','temporary-contract','part-time-contract','daily-paid-contract') NOT NULL");

        // Step 2: map old values to new ones
        DB::statement("UPDATE jobs SET employment_type = 'permanent-contract' WHERE employment_type = 'full-time'");
        DB::statement("UPDATE jobs SET employment_type = 'part-time-contract' WHERE employment_type = 'part-time'");
        DB::statement("UPDATE jobs SET employment_type = 'temporary-contract' WHERE employment_type = 'contract'");

        // Step 3: drop old values from enum
        DB::statement("ALTER TABLE jobs MODIFY COLUMN employment_type ENUM('permanent-contract','temporary-contract','part-time-contract','daily-paid-contract') NOT NULL");
    }

    public function down(): void
    {
        // Restore previous enum values if known; otherwise leave as-is
        DB::statement("ALTER TABLE jobs MODIFY COLUMN employment_type ENUM('permanent-contract','temporary-contract','part-time-contract','daily-paid-contract') NOT NULL");
    }
};
