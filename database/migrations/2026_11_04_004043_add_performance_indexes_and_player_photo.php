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
        Schema::table('fixtures', function (Blueprint $table) {
            $table->index('status', 'fixtures_status_idx');
            $table->index(['edition_id', 'status'], 'fixtures_edition_status_idx');
            $table->index(['status', 'match_date'], 'fixtures_status_match_date_idx');
        });

        Schema::table('players', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('contact');
            $table->index('reg_no', 'players_reg_no_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropIndex('players_reg_no_idx');
            $table->dropColumn('photo');
        });

        Schema::table('fixtures', function (Blueprint $table) {
            $table->dropIndex('fixtures_status_idx');
            $table->dropIndex('fixtures_edition_status_idx');
            $table->dropIndex('fixtures_status_match_date_idx');
        });
    }
};
