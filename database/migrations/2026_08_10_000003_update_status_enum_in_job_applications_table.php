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
            DB::statement("UPDATE jobapplications SET status = 'submitted' WHERE status = 'pending'");
            DB::statement('ALTER TABLE jobapplications DROP CONSTRAINT IF EXISTS jobapplications_status_check');
            DB::statement("ALTER TABLE jobapplications ADD CONSTRAINT jobapplications_status_check CHECK (status IN ('submitted', 'reviewed', 'shortlisted', 'rejected', 'hired'))");
            DB::statement("ALTER TABLE jobapplications ALTER COLUMN status SET DEFAULT 'submitted'");
            return;
        }

        DB::statement("ALTER TABLE jobapplications MODIFY COLUMN status ENUM('pending','submitted','reviewed','shortlisted','rejected','hired') NOT NULL DEFAULT 'pending'");

        // Step 2: remap old default value to new one
        DB::statement("UPDATE jobapplications SET status = 'submitted' WHERE status = 'pending'");

        // Step 3: finalise enum with 'submitted' as default, dropping 'pending'
        DB::statement("ALTER TABLE jobapplications MODIFY COLUMN status ENUM('submitted','reviewed','shortlisted','rejected','hired') NOT NULL DEFAULT 'submitted'");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("UPDATE jobapplications SET status = 'pending' WHERE status = 'submitted'");
            DB::statement('ALTER TABLE jobapplications DROP CONSTRAINT IF EXISTS jobapplications_status_check');
            DB::statement("ALTER TABLE jobapplications ADD CONSTRAINT jobapplications_status_check CHECK (status IN ('pending', 'reviewed', 'shortlisted', 'rejected', 'hired'))");
            DB::statement("ALTER TABLE jobapplications ALTER COLUMN status SET DEFAULT 'pending'");
            return;
        }

        DB::statement("ALTER TABLE jobapplications MODIFY COLUMN status ENUM('submitted','pending','reviewed','shortlisted','rejected','hired') NOT NULL DEFAULT 'submitted'");
        DB::statement("UPDATE jobapplications SET status = 'pending' WHERE status = 'submitted'");
        DB::statement("ALTER TABLE jobapplications MODIFY COLUMN status ENUM('pending','reviewed','shortlisted','rejected','hired') NOT NULL DEFAULT 'pending'");
    }
};
