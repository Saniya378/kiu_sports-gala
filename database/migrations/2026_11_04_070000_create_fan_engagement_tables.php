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
        Schema::create('match_fan_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixture_id')->constrained('fixtures')->cascadeOnDelete();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->string('session_id', 100);
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->unique(['fixture_id', 'session_id']);
            $table->index(['fixture_id', 'team_id']);
        });

        Schema::create('match_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixture_id')->constrained('fixtures')->cascadeOnDelete();
            $table->string('reaction', 20); // fire, clap, cheer, trophy
            $table->unsignedInteger('count')->default(0);
            $table->timestamps();

            $table->unique(['fixture_id', 'reaction']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_reactions');
        Schema::dropIfExists('match_fan_votes');
    }
};

