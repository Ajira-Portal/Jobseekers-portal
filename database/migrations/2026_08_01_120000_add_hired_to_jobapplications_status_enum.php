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
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE jobapplications DROP CONSTRAINT IF EXISTS jobapplications_status_check');
            DB::statement("ALTER TABLE jobapplications ADD CONSTRAINT jobapplications_status_check CHECK (status IN ('submitted', 'reviewed', 'shortlisted', 'rejected', 'hired'))");
            DB::statement("ALTER TABLE jobapplications ALTER COLUMN status SET DEFAULT 'submitted'");
            return;
        }

        DB::statement("ALTER TABLE jobapplications MODIFY COLUMN status ENUM('submitted','reviewed','shortlisted','rejected','hired') NOT NULL DEFAULT 'submitted'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("UPDATE jobapplications SET status = 'reviewed' WHERE status = 'hired'");

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE jobapplications DROP CONSTRAINT IF EXISTS jobapplications_status_check');
            DB::statement("ALTER TABLE jobapplications ADD CONSTRAINT jobapplications_status_check CHECK (status IN ('submitted', 'reviewed', 'shortlisted', 'rejected'))");
            DB::statement("ALTER TABLE jobapplications ALTER COLUMN status SET DEFAULT 'submitted'");
            return;
        }

        DB::statement("ALTER TABLE jobapplications MODIFY COLUMN status ENUM('submitted','reviewed','shortlisted','rejected') NOT NULL DEFAULT 'submitted'");
    }
};
