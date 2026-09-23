<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        $table = Schema::hasTable('job_applications') ? 'job_applications' : 'jobapplications';

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("UPDATE {$table} SET status = 'submitted' WHERE status = 'pending'");
            DB::statement("ALTER TABLE {$table} DROP CONSTRAINT IF EXISTS {$table}_status_check");
            DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$table}_status_check CHECK (status IN ('submitted', 'reviewed', 'shortlisted', 'rejected', 'hired'))");
            DB::statement("ALTER TABLE {$table} ALTER COLUMN status SET DEFAULT 'submitted'");
            return;
        }

        DB::statement("ALTER TABLE {$table} MODIFY COLUMN status ENUM('pending','submitted','reviewed','shortlisted','rejected','hired') NOT NULL DEFAULT 'pending'");

        // Step 2: remap old default value to new one
        DB::statement("UPDATE {$table} SET status = 'submitted' WHERE status = 'pending'");

        // Step 3: finalise enum with 'submitted' as default, dropping 'pending'
        DB::statement("ALTER TABLE {$table} MODIFY COLUMN status ENUM('submitted','reviewed','shortlisted','rejected','hired') NOT NULL DEFAULT 'submitted'");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        $table = Schema::hasTable('job_applications') ? 'job_applications' : 'jobapplications';

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("UPDATE {$table} SET status = 'pending' WHERE status = 'submitted'");
            DB::statement("ALTER TABLE {$table} DROP CONSTRAINT IF EXISTS {$table}_status_check");
            DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$table}_status_check CHECK (status IN ('pending', 'reviewed', 'shortlisted', 'rejected', 'hired'))");
            DB::statement("ALTER TABLE {$table} ALTER COLUMN status SET DEFAULT 'pending'");
            return;
        }

        DB::statement("ALTER TABLE {$table} MODIFY COLUMN status ENUM('submitted','pending','reviewed','shortlisted','rejected','hired') NOT NULL DEFAULT 'submitted'");
        DB::statement("UPDATE {$table} SET status = 'pending' WHERE status = 'submitted'");
        DB::statement("ALTER TABLE {$table} MODIFY COLUMN status ENUM('pending','reviewed','shortlisted','rejected','hired') NOT NULL DEFAULT 'pending'");
    }
};
