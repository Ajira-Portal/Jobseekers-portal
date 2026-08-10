<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobseeker_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('jobseeker_profiles', 'resident_number')) {
                $table->string('resident_number')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('jobseeker_profiles', 'disability_description')) {
                $table->string('disability_description')->nullable()->after('disability');
            }
        });

        Schema::table('personal_infos', function (Blueprint $table) {
            if (!Schema::hasColumn('personal_infos', 'disability_description')) {
                $table->string('disability_description')->nullable()->after('disability');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jobseeker_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('jobseeker_profiles', 'disability_description')) {
                $table->dropColumn('disability_description');
            }
            if (Schema::hasColumn('jobseeker_profiles', 'resident_number')) {
                $table->dropColumn('resident_number');
            }
        });

        Schema::table('personal_infos', function (Blueprint $table) {
            if (Schema::hasColumn('personal_infos', 'disability_description')) {
                $table->dropColumn('disability_description');
            }
        });
    }
};
