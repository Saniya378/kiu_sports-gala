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
        Schema::create('student_trials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edition_id')->constrained('editions')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->foreignId('sport_id')->constrained('sports')->cascadeOnDelete();
            $table->string('student_name');
            $table->string('reg_no');
            $table->string('gender')->default('male'); // male, female
            $table->string('contact');
            $table->string('experience_level')->default('intermediate'); // beginner, intermediate, advanced, varsity
            $table->string('playing_position')->nullable();
            $table->text('additional_notes')->nullable();
            $table->string('status')->default('pending'); // pending, shortlisted, selected, rejected
            $table->text('coordinator_notes')->nullable();
            $table->timestamps();

            $table->unique(['edition_id', 'department_id', 'sport_id', 'reg_no'], 'trial_student_sport_unique');
            $table->index(['department_id', 'sport_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_trials');
    }
};
