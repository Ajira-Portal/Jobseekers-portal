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
        Schema::create('education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Education Type
            $table->enum('type', ['informal', 'primary', 'secondary', 'university'])->default('university');
            
            // Institution Details
            $table->string('institution_name')->nullable(); // University/College name
            $table->string('school_name')->nullable(); // Primary/Secondary school name
            $table->string('course')->nullable(); // Course name
            $table->string('level')->nullable(); // diploma, bachelor, master, phd
            $table->text('description')->nullable(); // Informal education description
            
            // Dates
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'is_current']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education');
    }
};