<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opname_darah', function (Blueprint $table) {
            $table->id();
            $table->string('no_opname')->unique();
            $table->date('tgl_opname');
            $table->string('lokasi_opname')->nullable();
            $table->unsignedBigInteger('lokasi_opname_id')->nullable(); // ← hapus ->after()
            $table->enum('status', ['draft', 'selesai'])->default('draft');
            $table->text('keterangan')->nullable();

            $table->unsignedBigInteger('petugas_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->foreign('lokasi_opname_id')->references('id')->on('bagian_petugas')->nullOnDelete();
            $table->foreign('petugas_id')->references('id')->on('petugas')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('petugas')->nullOnDelete();
            $table->foreign('approved_by')->references('id')->on('petugas')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('opname_darah_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('opname_darah_id');
            $table->string('no_stok');
            $table->unsignedBigInteger('stok_darah_id')->nullable();

            $table->string('jenis_darah')->nullable();
            $table->string('golongan_darah')->nullable();
            $table->string('rhesus')->nullable();
            $table->date('tgl_kadaluarsa')->nullable();

            $table->integer('jumlah_sistem')->default(0);  // saldo dari sistem
            $table->integer('jumlah_fisik')->default(0);   // hasil hitung fisik
            $table->integer('selisih')->default(0);        // fisik - sistem

            $table->text('keterangan')->nullable();

            $table->foreign('opname_darah_id')->references('id')->on('opname_darah')->cascadeOnDelete();
            $table->foreign('stok_darah_id')->references('id')->on('stok_darah')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opname_darah_detail');
        Schema::dropIfExists('opname_darah');
    }
};