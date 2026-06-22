<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengembalian_darah_referal', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kembali', 20)->unique()->comment('Nomor Pengembalian');
            $table->date('tanggal_kembali')->comment('Tanggal Pengembalian');

            // Petugas
            $table->string('kode_petugas', 10)->nullable()->comment('Kode Petugas Penerima');
            $table->string('nama_petugas', 100)->nullable()->comment('Nama Petugas Penerima');

            // FPUP / Pasien
            $table->string('no_fpup', 20)->nullable()->comment('Nomor FPUP');
            $table->date('tgl_fpup')->nullable()->comment('Tanggal FPUP');
            $table->string('no_stock', 20)->nullable()->comment('Nomor Stock');

            // Rumah Sakit
            $table->string('kode_rumah_sakit', 10)->nullable();
            $table->string('nama_rumah_sakit', 150)->nullable();

            // Alasan & Status
            $table->string('alasan_kembali', 255)->nullable()->comment('Alasan Pengembalian');
            $table->enum('status_kembali', ['Baik', 'Rusak', 'Kadaluarsa'])->default('Baik');

            // Tanggal Pemberian
            $table->date('tgl_pemberian')->nullable()->comment('Tanggal Pemberian Darah');
            $table->integer('umur_hari_pemberian')->nullable()->comment('Umur (Hari) saat Pemberian');

            $table->string('yang_mengembalikan', 100)->nullable()->comment('Nama yang Mengembalikan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('no_fpup');
            $table->index('tanggal_kembali');
            $table->index('nomor_kembali');
        });
         Schema::create('pengembalian_darah_referal_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pengembalian_id');
            $table->string('no_stock', 50)->comment('No Stock Kantong Darah');
            $table->string('jenis_darah', 50)->nullable()->comment('Jenis/Komponen Darah');
            $table->string('gol_darah', 20)->nullable()->comment('Golongan Darah');
            $table->string('rhesus', 20)->nullable()->comment('Rhesus (+ / -)');
            $table->string('sts', 50)->nullable()->comment('Status');
            $table->enum('status_kembali', ['Baik', 'Rusak', 'Kadaluarsa'])->default('Baik');
            $table->string('alasan_kembali', 255)->nullable();
            $table->date('tgl_aftap')->nullable()->comment('Tanggal Aftap / Pengambilan');
            $table->date('kadaluarsa')->nullable()->comment('Tanggal Kadaluarsa');
            $table->integer('jumlah')->default(1);
            $table->text('keterangan')->nullable();
            $table->timestamps();
 
            $table->foreign('pengembalian_id')
                  ->references('id')
                  ->on('pengembalian_darah_referal')
                  ->onDelete('cascade');
            $table->index('no_stock');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengembalian_darah_referal_detail');
        Schema::dropIfExists('pengembalian_darah_referal');
    }
};