<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel ini menyimpan transaksi PEMBAYARAN atas biaya pengiriman darah
     * (dropping) ke Bank Darah / Rumah Sakit tujuan eksternal.
     *
     * Relasi: pembayaran_dropping_external -> pengiriman_darah_external (1-1 per nomor kirim)
     */
    public function up(): void
    {
        Schema::create('pembayaran_dropping_external', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengiriman_id')
                ->comment('FK ke pengiriman_darah_external.id yang dibayar')
                ->constrained('pengiriman_darah_external')
                ->cascadeOnDelete();

            // Disalin (denormalisasi) dari header pengiriman agar histori tidak berubah
            // walau data pengiriman induk diedit di kemudian hari.
            $table->string('nomor_kirim')->index();
            $table->dateTime('tanggal_kirim')->nullable();
            $table->string('institusi_tujuan')->nullable();
            $table->string('jenis_biaya')->default('DROPPING');

            $table->decimal('harus_dibayar', 15, 2)->default(0);
            $table->decimal('pembayaran', 15, 2)->default(0);

            $table->enum('metode_bayar', ['tunai', 'kredit'])->default('tunai');
            $table->dateTime('tanggal_bayar');

            $table->string('kode_kasir', 20)->nullable();
            $table->string('nama_kasir')->nullable();

            $table->text('keterangan')->nullable();
            $table->enum('status', ['lunas', 'belum_lunas', 'batal'])->default('lunas');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_dropping_external');
    }
};
