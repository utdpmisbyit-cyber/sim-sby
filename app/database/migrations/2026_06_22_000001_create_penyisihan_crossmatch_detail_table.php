<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyisihan_crossmatch_details', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('penyisihan_crossmatch_id');
            $table->unsignedBigInteger('cross_test_id')->nullable();

            // Mirror data dari cross_tests pada saat scan No Stock
            $table->string('no_stock', 30);
            $table->string('jns_darah', 50)->nullable();
            $table->string('gol_rh_kantong', 10)->nullable();
            $table->string('gol', 10)->nullable();
            $table->string('rhesus', 10)->nullable();
            $table->date('tgl_aftap')->nullable();
            $table->date('tgl_kadaluarsa')->nullable();
            $table->string('status_kantong', 30)->nullable(); // snapshot status cross_test saat disisihkan

            // Alasan kini langsung jadi kolom di table ini (bukan table master terpisah lagi)
            $table->enum('alasan', [
                'Selang Pendek',
                'Bocor',
                'Pasien Meninggal',
                'Keruh',
                'Expired Date',
                'Lab Luar Reaktif',
                'Darah Tidak Terserap',
                'DCT Positif / Mayor Positif',
            ]);

            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('penyisihan_crossmatch_id')
                  ->references('id')->on('penyisihan_crossmatch')
                  ->onDelete('cascade');

            $table->foreign('cross_test_id')
                  ->references('id')->on('cross_tests')
                  ->onDelete('set null');

            $table->index('no_stock');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyisihan_crossmatch_details');
    }
};