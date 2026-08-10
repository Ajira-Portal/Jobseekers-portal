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
            if (!Schema::hasColumn('jobseeker_profiles', 'has_passport')) {
                $table->enum('has_passport', ['yes', 'no'])->nullable()->after('disability');
            }

            if (!Schema::hasColumn('jobseeker_profiles', 'passport_number')) {
                $table->string('passport_number', 50)->nullable()->after('has_passport');
            }

            if (!Schema::hasColumn('jobseeker_profiles', 'passport_expiry_date')) {
                $table->date('passport_expiry_date')->nullable()->after('passport_number');
            }

            if (!Schema::hasColumn('jobseeker_profiles', 'place_of_issue')) {
                $table->enum('place_of_issue', ['tanzania_mainland', 'zanzibar'])->nullable()->after('shehia_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobseeker_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('jobseeker_profiles', 'place_of_issue')) {
                $table->dropColumn('place_of_issue');
            }

            if (Schema::hasColumn('jobseeker_profiles', 'passport_expiry_date')) {
                $table->dropColumn('passport_expiry_date');
            }

            if (Schema::hasColumn('jobseeker_profiles', 'passport_number')) {
                $table->dropColumn('passport_number');
            }

            if (Schema::hasColumn('jobseeker_profiles', 'has_passport')) {
                $table->dropColumn('has_passport');
            }
        });
    }
};