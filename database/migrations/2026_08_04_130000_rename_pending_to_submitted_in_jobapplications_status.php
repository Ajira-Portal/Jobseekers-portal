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
        DB::statement("UPDATE job_applications SET status = 'submitted' WHERE status = 'pending'");

        DB::statement("ALTER TABLE job_applications MODIFY COLUMN status ENUM('submitted','reviewed','shortlisted','rejected','hired') NOT NULL DEFAULT 'submitted'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE job_applications SET status = 'pending' WHERE status = 'submitted'");

        DB::statement("ALTER TABLE job_applications MODIFY COLUMN status ENUM('pending','reviewed','shortlisted','rejected','hired') NOT NULL DEFAULT 'pending'");
    }
};
