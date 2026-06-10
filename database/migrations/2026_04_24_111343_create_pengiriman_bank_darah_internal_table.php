<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengiriman_bank_darah_internal', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('permintaan_darah_penyimpanan_id');
            $table->string('no_pengiriman', 30)->unique();
            $table->string('no_permintaan', 30);

            $table->dateTime('tanggal_pengiriman');

            $table->unsignedBigInteger('petugas_id')->nullable();
            $table->string('petugas_kode', 20)->nullable();
            $table->string('petugas_nama', 150)->nullable();

            $table->string('bank_darah_kode', 20)->nullable();
            $table->string('bank_darah_nama', 150)->nullable();

            $table->enum('status', [
                'draft',
                'selesai'
            ])->default('selesai');

            $table->text('keterangan')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign(
                'permintaan_darah_penyimpanan_id',
                'fk_pengiriman_internal_permintaan'
            )
                ->references('id')
                ->on('permintaan_darah_penyimpanan')
                ->onDelete('cascade');

            $table->index('no_pengiriman');
            $table->index('no_permintaan');
            $table->index('tanggal_pengiriman');
        });

        Schema::create('pengiriman_bank_darah_internal_detail', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('pengiriman_bank_darah_internal_id');
            $table->unsignedBigInteger('permintaan_detail_id');
            $table->unsignedBigInteger('stok_id');

            $table->string('no_stok', 30);
            $table->string('no_kantong', 30)->nullable();

            $table->string('jenis_darah', 50);
            $table->string('golongan_darah', 10);
            $table->string('rhesus', 10);

            $table->date('tgl_expired')->nullable();

            $table->integer('gr')->default(0);
            $table->integer('ml')->default(0);
            $table->integer('jumlah')->default(1);

            $table->string('skrining', 50)->nullable();

            $table->enum('status', [
                'terkirim'
            ])->default('terkirim');

            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->foreign(
                'pengiriman_bank_darah_internal_id',
                'fk_pengiriman_internal_detail'
            )
                ->references('id')
                ->on('pengiriman_bank_darah_internal')
                ->onDelete('cascade');

            $table->index('no_stok');
            $table->index('stok_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'pengiriman_bank_darah_internal_detail'
        );

        Schema::dropIfExists(
            'pengiriman_bank_darah_internal'
        );
    }
};