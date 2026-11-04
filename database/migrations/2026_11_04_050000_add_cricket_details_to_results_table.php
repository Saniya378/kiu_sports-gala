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
        Schema::table('results', function (Blueprint $table) {
            $table->unsignedTinyInteger('team1_wickets')->nullable()->after('team1_score');
            $table->string('team1_overs', 10)->nullable()->after('team1_wickets');
            $table->unsignedTinyInteger('team2_wickets')->nullable()->after('team2_score');
            $table->string('team2_overs', 10)->nullable()->after('team2_wickets');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn(['team1_wickets', 'team1_overs', 'team2_wickets', 'team2_overs']);
        });
    }
};

