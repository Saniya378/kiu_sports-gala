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
        Schema::table('winners', function (Blueprint $table) {
            $table->foreignId('second_runner_up_department_id')
                ->nullable()
                ->after('runner_up_department_id')
                ->constrained('departments')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('winners', function (Blueprint $table) {
            $table->dropConstrainedForeignId('second_runner_up_department_id');
        });
    }
};
