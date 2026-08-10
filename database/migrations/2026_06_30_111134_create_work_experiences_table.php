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
        Schema::create('work_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Company Details
            $table->string('company_name');
            $table->string('position');
            $table->string('location')->nullable();
            
            // Employment Details
            $table->enum('employment_type', ['full-time', 'part-time', 'contract', 'internship', 'remote'])
                  ->default('full-time');
            
            // Description
            $table->text('description')->nullable();
            
            // Dates
            $table->date('start_date');
            $table->date('end_date')->nullable();
            
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_id', 'employment_type']);
            $table->index(['user_id', 'is_current']);
            $table->index(['start_date', 'end_date']);
            $table->index('company_name');
            $table->index('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_experiences');
    }
};