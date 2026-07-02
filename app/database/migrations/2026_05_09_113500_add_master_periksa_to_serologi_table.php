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
            $table->foreignId('jenis_periksa_serologi_id')
                ->nullable()
                ->after('tanggal')
                ->constrained('jenis_periksa_serologi')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('metode_serologi_id')
                ->nullable()
                ->after('jenis_periksa_serologi_id')
                ->constrained('metode_serologi')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('reagen_serologi_id')
                ->nullable()
                ->after('metode_serologi_id')
                ->constrained('reagen_serologi')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('serologi', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reagen_serologi_id');
            $table->dropConstrainedForeignId('metode_serologi_id');
            $table->dropConstrainedForeignId('jenis_periksa_serologi_id');
        });
    }
};
