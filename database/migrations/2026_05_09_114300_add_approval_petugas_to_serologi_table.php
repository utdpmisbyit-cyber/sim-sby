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
            $table->foreignId('diputar_oleh_id')
                ->nullable()
                ->after('pemeriksa_serologi_id')
                ->constrained('petugas')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignId('diperiksa_oleh_id')
                ->nullable()
                ->after('diputar_oleh_id')
                ->constrained('petugas')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignId('disahkan_oleh_id')
                ->nullable()
                ->after('diperiksa_oleh_id')
                ->constrained('petugas')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('serologi', function (Blueprint $table) {
            $table->dropConstrainedForeignId('disahkan_oleh_id');
            $table->dropConstrainedForeignId('diperiksa_oleh_id');
            $table->dropConstrainedForeignId('diputar_oleh_id');
        });
    }
};
