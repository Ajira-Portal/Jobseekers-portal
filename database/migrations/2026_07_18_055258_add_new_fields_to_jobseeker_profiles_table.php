<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jobseeker_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('jobseeker_profiles', 'disability')) {
                $table->enum('disability', ['yes', 'no'])->nullable()->after('date_of_birth');
            }

            if (!Schema::hasColumn('jobseeker_profiles', 'region_id')) {
                $table->unsignedBigInteger('region_id')->nullable()->after('profile_image');
                $table->index('region_id');
            }

            if (!Schema::hasColumn('jobseeker_profiles', 'district_id')) {
                $table->unsignedBigInteger('district_id')->nullable()->after('region_id');
                $table->index('district_id');
            }

            if (!Schema::hasColumn('jobseeker_profiles', 'ward_id')) {
                $table->unsignedBigInteger('ward_id')->nullable()->after('district_id');
                $table->index('ward_id');
            }

            if (!Schema::hasColumn('jobseeker_profiles', 'shehia_id')) {
                $table->unsignedBigInteger('shehia_id')->nullable()->after('ward_id');
                $table->index('shehia_id');
            }

            if (!Schema::hasColumn('jobseeker_profiles', 'is_completed')) {
                $table->boolean('is_completed')->default(false)->after('profile_completed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobseeker_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('jobseeker_profiles', 'is_completed')) {
                $table->dropColumn('is_completed');
            }

            if (Schema::hasColumn('jobseeker_profiles', 'shehia_id')) {
                $table->dropIndex(['shehia_id']);
                $table->dropColumn('shehia_id');
            }

            if (Schema::hasColumn('jobseeker_profiles', 'ward_id')) {
                $table->dropIndex(['ward_id']);
                $table->dropColumn('ward_id');
            }

            if (Schema::hasColumn('jobseeker_profiles', 'district_id')) {
                $table->dropIndex(['district_id']);
                $table->dropColumn('district_id');
            }

            if (Schema::hasColumn('jobseeker_profiles', 'region_id')) {
                $table->dropIndex(['region_id']);
                $table->dropColumn('region_id');
            }

            if (Schema::hasColumn('jobseeker_profiles', 'disability')) {
                $table->dropColumn('disability');
            }
        });
    }
};
