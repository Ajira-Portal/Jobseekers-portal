<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: expand enum to include both old and new values
        DB::statement("ALTER TABLE job_applications MODIFY COLUMN status ENUM('pending','submitted','reviewed','shortlisted','rejected','hired') NOT NULL DEFAULT 'pending'");

        // Step 2: remap old default value to new one
        DB::statement("UPDATE job_applications SET status = 'submitted' WHERE status = 'pending'");

        // Step 3: finalise enum with 'submitted' as default, dropping 'pending'
        DB::statement("ALTER TABLE job_applications MODIFY COLUMN status ENUM('submitted','reviewed','shortlisted','rejected','hired') NOT NULL DEFAULT 'submitted'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE job_applications MODIFY COLUMN status ENUM('submitted','pending','reviewed','shortlisted','rejected','hired') NOT NULL DEFAULT 'submitted'");
        DB::statement("UPDATE job_applications SET status = 'pending' WHERE status = 'submitted'");
        DB::statement("ALTER TABLE job_applications MODIFY COLUMN status ENUM('pending','reviewed','shortlisted','rejected','hired') NOT NULL DEFAULT 'pending'");
    }
};
