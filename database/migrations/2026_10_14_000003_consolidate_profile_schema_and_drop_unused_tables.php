<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('jobseeker_profiles', 'profile_completed')) {
            DB::table('jobseeker_profiles')
                ->where('profile_completed', true)
                ->update(['is_completed' => true]);
        }

        // The legacy text columns shadowed the region()/district()/ward()/shehia() relations.
        $legacy = array_values(array_filter(
            ['region', 'district', 'ward', 'shehia', 'profile_completed'],
            fn ($column) => Schema::hasColumn('jobseeker_profiles', $column)
        ));

        if ($legacy) {
            Schema::table('jobseeker_profiles', function (Blueprint $table) use ($legacy) {
                $table->dropColumn($legacy);
            });
        }

        Schema::dropIfExists('work_experiences');
        Schema::dropIfExists('personal_infos');
    }

    public function down(): void
    {
        Schema::table('jobseeker_profiles', function (Blueprint $table) {
            $table->string('region')->nullable();
            $table->string('district')->nullable();
            $table->string('ward')->nullable();
            $table->string('shehia')->nullable();
            $table->boolean('profile_completed')->default(false);
        });
    }
};
