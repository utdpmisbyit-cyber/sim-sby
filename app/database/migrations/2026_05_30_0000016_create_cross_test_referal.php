<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cross_tests_referal', function (Blueprint $table) {
            $table->id();

            // Referensi ke header & detail permintaan FPUP Referal
            $table->unsignedBigInteger('permintaan_fpup_referal_id');
            $table->unsignedBigInteger('permintaan_fpup_referal_detail_id')->nullable();
            $table->string('no_fpup', 30);
            $table->string('no_referal', 30)->nullable();

            // Identitas Pasien (mirror dari FPUP Referal)
            $table->string('nama_pasien', 100)->nullable();
            $table->string('gol', 10)->nullable();
            $table->string('rhesus', 10)->nullable();

            // Identitas Kantong Darah
            $table->string('no_stock', 30)->nullable();
            $table->unsignedBigInteger('jenis_darah_id')->nullable();
            $table->string('jns_darah', 50)->nullable();      // mirror nama jenis darah (snapshot)
            $table->string('gol_rh_kantong', 10)->nullable();
            $table->date('tgl_ambil')->nullable();
            $table->date('tgl_produksi')->nullable();
            $table->date('tgl_kadaluarsa')->nullable();
            $table->date('tgl_online')->nullable();

            $table->string('referal')->nullable();         // YA / TIDAK (snapshot pasien_referal)
            $table->string('kurir_online')->nullable();
            $table->text('catatan_hasil')->nullable();

            // Status & Petugas
            $table->string('pemeriksa', 100)->nullable();
            $table->datetime('tgl_periksa')->nullable();
            $table->enum('status', ['pending', 'proses', 'compatible', 'incompatible', 'selesai'])
                  ->default('pending');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('permintaan_fpup_referal_id')
                  ->references('id')
                  ->on('permintaan_fpup_referal')
                  ->onDelete('cascade')
                  ->name('fk_ct_referal_to_fpup_referal');

            $table->foreign('permintaan_fpup_referal_detail_id')
                  ->references('id')
                  ->on('permintaan_fpup_referal_detail')
                  ->onDelete('set null')
                  ->name('fk_ct_referal_to_fpup_referal_detail');

            $table->foreign('jenis_darah_id')
                  ->references('id')
                  ->on('jenis_darah')
                  ->onDelete('set null')
                  ->name('fk_ct_referal_to_jenis_darah');

            $table->index('no_fpup');
            $table->index('no_stock');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cross_tests_referal');
    }
};