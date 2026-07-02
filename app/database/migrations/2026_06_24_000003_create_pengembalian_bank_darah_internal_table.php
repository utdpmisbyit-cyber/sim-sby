<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pengembalian_bank_darah_internal', function (Blueprint $table) {
            $table->id();
            $table->string('no_pengembalian')->unique();
            $table->date('tgl_pengembalian');

            // Unit yang mengembalikan (sebelumnya menerima kiriman)
            $table->unsignedBigInteger('bank_darah_asal_id')->nullable();
            // Unit yang menerima kembali (asal pengiriman semula)
            $table->unsignedBigInteger('bank_darah_tujuan_id')->nullable();

            // Petugas yang menyerahkan / mengembalikan
            $table->unsignedBigInteger('petugas_kembali_id')->nullable();
            // Petugas yang menerima di unit tujuan
            $table->unsignedBigInteger('petugas_terima_id')->nullable();

            $table->text('keterangan')->nullable();
            $table->string('status')->default('draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('bank_darah_asal_id')->references('id')->on('bank_darah')->nullOnDelete();
            $table->foreign('bank_darah_tujuan_id')->references('id')->on('bank_darah')->nullOnDelete();
            $table->foreign('petugas_kembali_id')->references('id')->on('petugas')->nullOnDelete();
            $table->foreign('petugas_terima_id')->references('id')->on('petugas')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('petugas')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('petugas')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pengembalian_bank_darah_internal_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pengembalian_id');
            $table->string('no_stok');
            $table->string('no_kantong')->nullable();
            $table->string('no_donor')->nullable();
            $table->unsignedBigInteger('stok_darah_id')->nullable();
            $table->string('jenis_darah')->nullable();
            $table->string('golongan_darah')->nullable();
            $table->string('rhesus')->nullable();
            $table->date('tgl_aftap')->nullable();
            $table->date('tgl_expired')->nullable();
            $table->string('status_stok')->nullable();
            $table->string('status_kembali')->nullable();
            $table->string('alasan_kembali')->nullable();
            $table->integer('jumlah')->default(1);
            $table->text('keterangan')->nullable();

            $table->foreign('pengembalian_id')
                  ->references('id')
                  ->on('pengembalian_bank_darah_internal')
                  ->cascadeOnDelete();

            $table->foreign('stok_darah_id')
                  ->references('id')
                  ->on('stok_darah')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengembalian_bank_darah_internal_detail');
        Schema::dropIfExists('pengembalian_bank_darah_internal');
    }
};