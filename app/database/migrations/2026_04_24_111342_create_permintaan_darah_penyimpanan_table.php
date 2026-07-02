<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permintaan_darah_penyimpanan', function (Blueprint $table) {
            $table->id();
            $table->string('no_permintaan', 20)->unique();
            $table->string('bank_darah_kode', 10);
            $table->string('bank_darah_nama', 100);
            $table->string('petugas_kode', 10)->nullable();
            $table->string('petugas_nama', 100)->nullable();
            $table->string('tipe', 10)->nullable()->comment('F4=Capping, dll');
            $table->date('tanggal_minta');
            $table->enum('status', ['permintaan', 'proses', 'selesai', 'batal'])->default('permintaan');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('no_permintaan');
            $table->index('tanggal_minta');
            $table->index('status');
        });

        Schema::create('permintaan_darah_penyimpanan_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('permintaan_darah_penyimpanan_id');
           $table->foreign(
        'permintaan_darah_penyimpanan_id',
        'fk_pdp_detail'
    )->references('id')
     ->on('permintaan_darah_penyimpanan')
     ->onDelete('cascade');
            $table->string('jenis_darah', 50);
            $table->string('golongan_darah', 5)->comment('A, B, AB, O');
            $table->string('rhesus', 10)->default('Positif')->comment('Positif / Negatif');
            $table->unsignedSmallInteger('jumlah_kantong')->default(1);
            $table->unsignedSmallInteger('jumlah_cc')->default(0);
            $table->date('tanggal_perlu')->nullable();
            $table->string('no_fpup', 30)->nullable();
            $table->string('nama_os', 100)->nullable()->comment('Nama pasien / OS');
            $table->enum('status', ['permintaan', 'proses', 'selesai', 'ditolak'])->default('permintaan');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('golongan_darah');
            $table->index('jenis_darah');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_darah_penyimpanan_detail');
        Schema::dropIfExists('permintaan_darah_penyimpanan');
    }
};