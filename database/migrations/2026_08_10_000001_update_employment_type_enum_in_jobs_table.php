<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("UPDATE jobs SET employment_type = 'permanent-contract' WHERE employment_type = 'full-time'");
            DB::statement("UPDATE jobs SET employment_type = 'part-time-contract' WHERE employment_type = 'part-time'");
            DB::statement("UPDATE jobs SET employment_type = 'temporary-contract' WHERE employment_type = 'contract'");
            DB::statement('ALTER TABLE jobs DROP CONSTRAINT IF EXISTS jobs_employment_type_check');
            DB::statement("ALTER TABLE jobs ADD CONSTRAINT jobs_employment_type_check CHECK (employment_type IN ('permanent-contract', 'temporary-contract', 'part-time-contract', 'daily-paid-contract'))");
            return;
        }

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
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("UPDATE jobs SET employment_type = 'full-time' WHERE employment_type = 'permanent-contract'");
            DB::statement("UPDATE jobs SET employment_type = 'part-time' WHERE employment_type = 'part-time-contract'");
            DB::statement("UPDATE jobs SET employment_type = 'contract' WHERE employment_type IN ('temporary-contract', 'daily-paid-contract')");
            DB::statement('ALTER TABLE jobs DROP CONSTRAINT IF EXISTS jobs_employment_type_check');
            DB::statement("ALTER TABLE jobs ADD CONSTRAINT jobs_employment_type_check CHECK (employment_type IN ('full-time', 'part-time', 'contract'))");
            return;
        }

        DB::statement("ALTER TABLE jobs MODIFY COLUMN employment_type ENUM('permanent-contract','temporary-contract','part-time-contract','daily-paid-contract') NOT NULL");
    }
};
