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
        // Drop table if exists (for fresh start)
        Schema::dropIfExists('jobseeker_infos');
        
        Schema::create('jobseeker_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Personal Information
            $table->string('phone')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('disability', ['yes', 'no'])->nullable();
            
            // Location Fields
            $table->unsignedBigInteger('region_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('ward_id')->nullable();
            $table->unsignedBigInteger('shehia_id')->nullable();
            
            // Files
            $table->string('resume')->nullable();
            $table->string('profile_image')->nullable();
            
            // Profile Status
            $table->boolean('step1_completed')->default(false);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('region_id');
            $table->index('district_id');
            $table->index('ward_id');
            $table->index('shehia_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobseeker_infos');
    }
};
