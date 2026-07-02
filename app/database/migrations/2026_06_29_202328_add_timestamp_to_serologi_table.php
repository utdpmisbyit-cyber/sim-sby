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
        Schema::table('serologi', function (Blueprint $table) {
            $table->dateTime('time_in')->nullable();
            $table->dateTime('time_process')->nullable();
            $table->dateTime('time_complete')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('serologi', function (Blueprint $table) {
            $table->dropColumn('time_in');
            $table->dropColumn('time_process');
            $table->dropColumn('time_complete');
        });
    }
};
