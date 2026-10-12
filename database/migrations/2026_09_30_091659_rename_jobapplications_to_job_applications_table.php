<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename the existing table to Laravel's standard name
        if (Schema::hasTable('jobapplications') && !Schema::hasTable('job_applications')) {
            Schema::rename('jobapplications', 'job_applications');
        }

        // Add the new columns
        Schema::table('job_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('job_applications', 'post_reference')) {
                $table->string('post_reference')->nullable();
            }

            if (!Schema::hasColumn('job_applications', 'interview_number')) {
                $table->string('interview_number')->nullable();
            }
        });

        // Add unique constraint to prevent duplicate applications
        DB::statement('
            CREATE UNIQUE INDEX IF NOT EXISTS job_applications_job_id_jobseeker_id_unique
            ON job_applications (job_id, jobseeker_id)
        ');

        // Add indexes
        DB::statement('
            CREATE INDEX IF NOT EXISTS job_applications_jobseeker_id_index
            ON job_applications (jobseeker_id)
        ');

        DB::statement('
            CREATE INDEX IF NOT EXISTS job_applications_status_index
            ON job_applications (status)
        ');

        DB::statement('
            CREATE INDEX IF NOT EXISTS job_applications_created_at_index
            ON job_applications (created_at)
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('job_applications')) {

            // Remove indexes
            DB::statement('
                DROP INDEX IF EXISTS job_applications_job_id_jobseeker_id_unique
            ');

            DB::statement('
                DROP INDEX IF EXISTS job_applications_jobseeker_id_index
            ');

            DB::statement('
                DROP INDEX IF EXISTS job_applications_status_index
            ');

            DB::statement('
                DROP INDEX IF EXISTS job_applications_created_at_index
            ');

            // Remove newly added columns
            Schema::table('job_applications', function (Blueprint $table) {
                if (Schema::hasColumn('job_applications', 'post_reference')) {
                    $table->dropColumn('post_reference');
                }

                if (Schema::hasColumn('job_applications', 'interview_number')) {
                    $table->dropColumn('interview_number');
                }
            });

            // Rename back
            if (!Schema::hasTable('jobapplications')) {
                Schema::rename('job_applications', 'jobapplications');
            }
        }
    }
};
