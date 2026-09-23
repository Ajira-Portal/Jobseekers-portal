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
<<<<<<< HEAD
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("UPDATE jobapplications SET status = 'submitted' WHERE status = 'pending'");

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE jobapplications DROP CONSTRAINT IF EXISTS jobapplications_status_check');
            DB::statement("ALTER TABLE jobapplications ADD CONSTRAINT jobapplications_status_check CHECK (status IN ('submitted', 'reviewed', 'shortlisted', 'rejected', 'hired'))");
            DB::statement("ALTER TABLE jobapplications ALTER COLUMN status SET DEFAULT 'submitted'");
            return;
        }

        DB::statement("ALTER TABLE jobapplications MODIFY COLUMN status ENUM('submitted','reviewed','shortlisted','rejected','hired') NOT NULL DEFAULT 'submitted'");
=======
        DB::statement("UPDATE job_applications SET status = 'submitted' WHERE status = 'pending'");

        DB::statement("ALTER TABLE job_applications MODIFY COLUMN status ENUM('submitted','reviewed','shortlisted','rejected','hired') NOT NULL DEFAULT 'submitted'");
>>>>>>> develop
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
<<<<<<< HEAD
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("UPDATE jobapplications SET status = 'pending' WHERE status = 'submitted'");

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE jobapplications DROP CONSTRAINT IF EXISTS jobapplications_status_check');
            DB::statement("ALTER TABLE jobapplications ADD CONSTRAINT jobapplications_status_check CHECK (status IN ('pending', 'reviewed', 'shortlisted', 'rejected', 'hired'))");
            DB::statement("ALTER TABLE jobapplications ALTER COLUMN status SET DEFAULT 'pending'");
            return;
        }

        DB::statement("ALTER TABLE jobapplications MODIFY COLUMN status ENUM('pending','reviewed','shortlisted','rejected','hired') NOT NULL DEFAULT 'pending'");
=======
        DB::statement("UPDATE job_applications SET status = 'pending' WHERE status = 'submitted'");

        DB::statement("ALTER TABLE job_applications MODIFY COLUMN status ENUM('pending','reviewed','shortlisted','rejected','hired') NOT NULL DEFAULT 'pending'");
>>>>>>> develop
    }
};
