<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pengiriman_darah_external', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pengiriman', 50)->unique();
            $table->foreignId('permintaan_id')->constrained('permintaan_darah_external')->onDelete('cascade');
            $table->string('no_permintaan', 50);
            $table->dateTime('tanggal_kirim');
            $table->string('petugas', 100);
            $table->string('petugas_kode', 50);
            $table->string('penerima', 100)->nullable();
            $table->string('institusi_tujuan', 150)->nullable();
            $table->enum('jenis_biaya', ['Dropping', 'Konfalesen', 'BPJS', 'ASURASI'])->default('Dropping');
            $table->enum('dropping', ['AMBIL_SENDIRI', 'DIANTAR', 'KURIR'])->nullable();
            $table->enum('status', ['TERKIRIM', 'PROSES', 'BATAL'])->default('PROSES');
            $table->decimal('suhu_kirim', 5, 1)->nullable()->comment('Suhu dalam Celsius');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('pengiriman_darah_external_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengiriman_id')->constrained('pengiriman_darah_external')->onDelete('cascade');
            $table->foreignId('permintaan_detail_id')->nullable()->constrained('permintaan_darah_external_detail')->onDelete('set null');
            $table->string('no_stock', 50);
            $table->string('jenis_darah', 50)->nullable();
            $table->enum('gol_darah', ['A', 'B', 'O', 'AB']);
            $table->enum('rhesus', ['Positif', 'Negatif']);
            $table->integer('jumlah')->default(1);
            $table->dateTime('tgl_kadaluarsa')->nullable();
            $table->boolean('nat')->default(false);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengiriman_darah_external_detail');
        Schema::dropIfExists('pengiriman_darah_external');
    }
};