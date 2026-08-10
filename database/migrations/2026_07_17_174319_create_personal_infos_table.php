<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (Schema::hasTable('personal_infos')) {
            return;
        }

        Schema::create('personal_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('phone')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('disability', ['yes', 'no'])->nullable();
            // Location fields
            $table->unsignedBigInteger('region_id')->nullable(false);
            $table->unsignedBigInteger('district_id')->nullable(false);
            $table->unsignedBigInteger('ward_id')->nullable(false);
            $table->unsignedBigInteger('shehia_id')->nullable(false);

            $table->string('resume')->nullable();
            $table->string('profile_image')->nullable();
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

    public function down()
    {
        Schema::dropIfExists('personal_infos');
    }
};
