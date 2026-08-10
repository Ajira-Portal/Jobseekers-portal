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
        Schema::table('graduates', function (Blueprint $table) {
            if (Schema::hasColumn('graduates', 'first_name')) {
                $table->dropColumn('first_name');
            }

            if (Schema::hasColumn('graduates', 'last_name')) {
                $table->dropColumn('last_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('graduates', function (Blueprint $table) {
            if (!Schema::hasColumn('graduates', 'first_name')) {
                $table->string('first_name')->nullable()->after('student_number');
            }

            if (!Schema::hasColumn('graduates', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }
        });
    }
};
