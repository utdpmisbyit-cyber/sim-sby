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
        Schema::table('serologi_detail', function (Blueprint $table) {
            $table->foreignId('aftap_id')
                ->nullable()
                ->after('serologi_id')
                ->constrained('aftap')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('serologi_detail', function (Blueprint $table) {
            $table->dropConstrainedForeignId('aftap_id');
        });
    }
};
