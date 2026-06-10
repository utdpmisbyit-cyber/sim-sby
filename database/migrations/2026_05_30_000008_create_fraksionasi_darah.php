<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fraksionasi_darah', function (Blueprint $table) {
            $table->id();
            $table->string('no_fraksionasi')->unique();
            $table->string('jenis_darah')->nullable();
            $table->string('golongan_darah')->nullable();
            $table->string('rhesus')->nullable();
            $table->string('no_kantong')->nullable();  
            $table->enum('ukuran_kantong', ['350', '450', '1000'])->default('450'); 
            $table->string('jenis_kantong')->nullable();  
            $table->string('tipe_kantong')->nullable();  
            $table->string('merk')->nullable();  
            
            $table->string('no_transaksi')->unique(); 
            $table->string('no_stok');

            $table->integer('suhu_box')->nullable();      
            $table->dateTime('tgl_dropping')->nullable();  
            $table->dateTime('tgl_produksi')->nullable();
            $table->dateTime('tgl_kadaluarsa')->nullable();
        
            $table->string('nomor_rak')->nullable();
            $table->string('nomor_box')->nullable();
            $table->enum('status', ['proses', 'selesai', 'batal'])->default('proses');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('pendataan_kantong_id')->nullable();
            $table->unsignedBigInteger('stok_darah_id')->nullable();
            $table->unsignedBigInteger('petugas_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('pendataan_kantong_id')
                  ->references('id')->on('pendataan_kantong')
                  ->nullOnDelete();

            $table->foreign('stok_darah_id')
                  ->references('id')->on('stok_darah')
                  ->nullOnDelete();

            $table->foreign('petugas_id')
                  ->references('id')->on('petugas')
                  ->nullOnDelete();

            $table->foreign('created_by')
                  ->references('id')->on('petugas')
                  ->nullOnDelete();

            $table->foreign('updated_by')
                  ->references('id')->on('petugas')
                  ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fraksionasi_darah');
    }
};