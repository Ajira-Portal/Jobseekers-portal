<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        $table = Schema::hasTable('job_applications') ? 'job_applications' : 'jobapplications';

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE {$table} DROP CONSTRAINT IF EXISTS {$table}_status_check");
            DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$table}_status_check CHECK (status IN ('submitted', 'reviewed', 'shortlisted', 'rejected', 'hired'))");
            DB::statement("ALTER TABLE {$table} ALTER COLUMN status SET DEFAULT 'submitted'");
            return;
        }

        DB::statement("ALTER TABLE {$table} MODIFY COLUMN status ENUM('submitted','reviewed','shortlisted','rejected','hired') NOT NULL DEFAULT 'submitted'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        $table = Schema::hasTable('job_applications') ? 'job_applications' : 'jobapplications';

        DB::statement("UPDATE {$table} SET status = 'reviewed' WHERE status = 'hired'");

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE {$table} DROP CONSTRAINT IF EXISTS {$table}_status_check");
            DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$table}_status_check CHECK (status IN ('submitted', 'reviewed', 'shortlisted', 'rejected'))");
            DB::statement("ALTER TABLE {$table} ALTER COLUMN status SET DEFAULT 'submitted'");
            return;
        }

        DB::statement("ALTER TABLE {$table} MODIFY COLUMN status ENUM('submitted','reviewed','shortlisted','rejected') NOT NULL DEFAULT 'submitted'");
    }
};
