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
        // Add hired as an allowed status.
        DB::statement("ALTER TABLE jobapplications MODIFY COLUMN status ENUM('pending','reviewed','shortlisted','rejected','hired') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Prevent rollback failure if any rows already use hired.
        DB::statement("UPDATE jobapplications SET status = 'reviewed' WHERE status = 'hired'");

        DB::statement("ALTER TABLE jobapplications MODIFY COLUMN status ENUM('pending','reviewed','shortlisted','rejected') NOT NULL DEFAULT 'pending'");
    }
};
