<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stok_darah', function (Blueprint $table) {
            $table->id();
            $table->string('no_stok')->unique();
            $table->string('no_kantong')->nullable();
            $table->string('jenis_darah')->nullable();
            $table->string('golongan_darah')->nullable();
            $table->string('rhesus')->nullable();
            $table->date('tgl_aftap')->nullable();
            $table->date('tgl_produksi')->nullable();
            $table->date('tgl_expired')->nullable();
            $table->string('ruang')->nullable();
            $table->integer('ml')->nullable();
            $table->integer('gr')->nullable();
            $table->string('skrining')->nullable();
            $table->string('no_fpd')->nullable();
            $table->unsignedBigInteger('asal_darah_id')->nullable();

            // Saldo stok
            $table->integer('jumlah_masuk')->default(0);
            $table->integer('jumlah_keluar')->default(0);
            $table->integer('jumlah_kembali')->default(0);
            $table->integer('saldo')->default(0);  // masuk - keluar + kembali

            // Status: tersedia | dipakai | kadaluarsa | dibuang
            $table->string('status_stok')->default('tersedia');

            $table->unsignedBigInteger('penerimaan_id')->nullable();
            $table->foreign('penerimaan_id')
                  ->references('id')
                  ->on('penerimaan_prolis_penyimpanan')
                  ->nullOnDelete();

            $table->foreign('asal_darah_id')
                  ->references('id')
                  ->on('asal_darah')
                  ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_darah');
    }
};