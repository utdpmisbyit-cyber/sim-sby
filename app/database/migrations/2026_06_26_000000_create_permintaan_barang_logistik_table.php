<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permintaan_barang_logistik', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('pengajuan_barang_id')
                ->constrained('pengajuan_barang')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->date('tgl_terima')->nullable();
            $table->date('tgl_proses')->nullable();
            $table->integer('jml_acc')->nullable();
            $table->foreignId('petugas_gudang_id')
                ->nullable()
                ->constrained('petugas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('status')->default('diterima'); // diterima, diproses, dikirim, selesai, ditolak
            $table->text('catatan')->nullable();
            $table->string('user_proses')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_barang_logistik');
    }
};