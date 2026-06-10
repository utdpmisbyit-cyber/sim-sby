<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transaksi_stok_darah', function (Blueprint $table) {
            $table->id();
            $table->string('no_stok');
            $table->unsignedBigInteger('stok_darah_id')->nullable();

            /**
             * Jenis transaksi:
             *   masuk       = penerimaan dari prolis
             *   keluar      = pengiriman (internal/eksternal)
             *   kembali     = pengembalian
             *   hapus       = soft-delete penerimaan
             */
            $table->enum('jenis', ['masuk', 'keluar', 'kembali', 'hapus']);

            $table->integer('jumlah')->default(1);
            $table->string('no_referensi')->nullable(); // no_penerimaan / no_pengiriman / no_pengembalian
            $table->string('sumber')->nullable();       // 'penerimaan' | 'pengiriman_internal' | dst
            $table->unsignedBigInteger('referensi_id')->nullable(); // id record sumber
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('petugas_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->foreign('stok_darah_id')
                  ->references('id')
                  ->on('stok_darah')
                  ->nullOnDelete();

            $table->foreign('petugas_id')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();

            $table->foreign('created_by')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_stok_darah');
    }
};