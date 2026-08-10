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
        Schema::table('jobs', function (Blueprint $table) {
            if (!Schema::hasColumn('jobs', 'is_rejected')) {
                $table->boolean('is_rejected')->default(false)->after('is_approved');
                $table->index('is_rejected');
            }

            if (!Schema::hasColumn('jobs', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('is_rejected');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            if (Schema::hasColumn('jobs', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }

            if (Schema::hasColumn('jobs', 'is_rejected')) {
                $table->dropIndex(['is_rejected']);
                $table->dropColumn('is_rejected');
            }
        });
    }
};
