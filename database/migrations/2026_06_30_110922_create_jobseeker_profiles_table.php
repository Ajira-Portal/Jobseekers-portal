<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobseeker_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Personal Information
            $table->string('phone')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->boolean('disability')->default(false);
            $table->string('region')->nullable(false);
            $table->string('district')->nullable(false);
            $table->string('ward')->nullable(false);
            $table->string('shehia')->nullable(false);
            

            // Documents
            $table->string('resume')->nullable();
            $table->string('profile_image')->nullable();
            
        
            // Status
            $table->boolean('profile_completed')->default(false);
            
            $table->timestamps();
            
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobseeker_profiles');
    }
};