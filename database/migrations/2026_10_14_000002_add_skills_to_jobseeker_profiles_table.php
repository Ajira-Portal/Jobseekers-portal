<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('jobseeker_profiles', 'skills')) {
            Schema::table('jobseeker_profiles', function (Blueprint $table) {
                $table->text('skills')->nullable();
            });
        }

        // Backfill from the skills table so previously saved skills show up again.
        if (Schema::hasTable('skills')) {
            DB::table('skills')->orderBy('id')->get()->groupBy('user_id')->each(function ($rows, $userId) {
                DB::table('jobseeker_profiles')
                    ->where('user_id', $userId)
                    ->whereNull('skills')
                    ->update(['skills' => $rows->pluck('skills_name')->implode(', ')]);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('jobseeker_profiles', 'skills')) {
            Schema::table('jobseeker_profiles', function (Blueprint $table) {
                $table->dropColumn('skills');
            });
        }
    }
};
