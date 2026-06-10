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
        Schema::create('tipe_kantong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_kantong_id')->constrained('jenis_kantong');
            $table->string('nama');
            $table->timestamps();
        });
        Schema::table('pemeriksaan_dokter', function (Blueprint $table) {
            $table->foreignId('tipe_kantong_id')->nullable()->constrained('tipe_kantong');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemeriksaan_dokter', function (Blueprint $table) {
            $table->dropForeign(['tipe_kantong_id']);
            $table->dropColumn('tipe_kantong_id');
        });
        Schema::dropIfExists('tipe_kantong');
    }
};
