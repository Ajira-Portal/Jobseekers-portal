<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $columns = ['region', 'district', 'ward', 'shehia'];

    public function up(): void
    {
        Schema::table('jobseeker_profiles', function (Blueprint $table) {
            foreach ($this->columns as $column) {
                if (Schema::hasColumn('jobseeker_profiles', $column)) {
                    $table->string($column)->nullable()->change();
                }
            }
        });
    }

    public function down(): void
    {
        // Intentionally left as-is: restoring NOT NULL would break profiles created without these legacy columns.
    }
};
