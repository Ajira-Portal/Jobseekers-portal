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
        Schema::create('graduates', function (Blueprint $table) {
            $table->id();
            $table->string('student_number')->nullable()->unique();
            $table->string('full_name')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('resident_number')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->string('region')->nullable();
            $table->string('district')->nullable();
            $table->string('ward')->nullable();
            $table->string('shehia')->nullable();
            $table->string('next_of_kin_phone')->nullable();

            $table->enum('graduate_category', ['college_university', 'vta', 'apprenticeship', 'other'])->default('other');
            $table->enum('education_type', ['university', 'vta', 'apprenticeship'])->nullable();
            $table->string('program_of_study')->nullable();
            $table->string('college_name')->nullable();
            $table->string('university_course')->nullable();
            $table->string('completed_year', 10)->nullable();
            $table->string('gpa', 20)->nullable();
            $table->string('nta_level', 50)->nullable();
            $table->string('vta_course')->nullable();
            $table->string('started_year', 10)->nullable();
            $table->string('ending_year', 10)->nullable();
            $table->string('apprenticeship_mode', 20)->nullable();
            $table->string('apprenticeship_specification')->nullable();

            $table->string('year_of_graduation', 10)->nullable();
            $table->string('cgpa', 20)->nullable();
            $table->text('skills')->nullable();
            $table->text('certifications')->nullable();
            $table->text('additional_notes')->nullable();

            $table->enum('status', ['active', 'inactive', 'employed', 'unemployed'])->default('active');
            $table->string('resume')->nullable();
            $table->string('profile_photo')->nullable();
            $table->enum('registered_from', ['welcome', 'admin', 'import'])->default('admin');
            $table->timestamps();

            $table->index('status');
            $table->index('graduate_category');
            $table->index('program_of_study');
            $table->index('year_of_graduation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('graduates');
    }
};
