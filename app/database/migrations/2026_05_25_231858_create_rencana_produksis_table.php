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
        Schema::create('rencana_produksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengiriman_sample_id')->constrained('pengiriman_sample');
            $table->date('tanggal');
            $table->foreignId('petugas_id')->constrained('petugas');
            $table->foreignId('tipe_kantong_id')->constrained('tipe_kantong');
            $table->string('jenis_darah');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rencana_produksi');
    }
};
