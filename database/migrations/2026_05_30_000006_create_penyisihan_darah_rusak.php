<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyisihan_darah_rusak', function (Blueprint $table) {
            $table->id();
            $table->string('no_penyisihan')->unique();
            $table->date('tgl_penyisihan');
            $table->string('alasan');         // rusak | kadaluarsa | lainnya
            $table->text('keterangan')->nullable();
            $table->string('status')->default('draft'); // draft | disetujui | ditolak

            $table->unsignedBigInteger('petugas_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->foreign('petugas_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('penyisihan_darah_rusak_detail', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('penyisihan_id');
            $table->foreign('penyisihan_id')
                  ->references('id')
                  ->on('penyisihan_darah_rusak')
                  ->cascadeOnDelete();

            // Relasi ke stok_darah
            $table->unsignedBigInteger('stok_darah_id')->nullable();
            $table->foreign('stok_darah_id')
                  ->references('id')
                  ->on('stok_darah')
                  ->nullOnDelete();

            // Relasi ke penerimaan_prolis_penyimpanan (opsional, bisa di-resolve via stok_darah)
            $table->unsignedBigInteger('penerimaan_id')->nullable();
            $table->foreign('penerimaan_id')
                  ->references('id')
                  ->on('penerimaan_prolis_penyimpanan')
                  ->nullOnDelete();

            // Snapshot data kantong saat penyisihan
            $table->string('no_stok');
            $table->string('jenis_darah')->nullable();
            $table->string('golongan_darah')->nullable();
            $table->string('rhesus')->nullable();
            $table->date('tgl_aftap')->nullable();
            $table->date('tgl_expired')->nullable();
            $table->string('status_detail')->default('pending'); // pending | diproses | selesai

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyisihan_darah_rusak_detail');
        Schema::dropIfExists('penyisihan_darah_rusak');
    }
};