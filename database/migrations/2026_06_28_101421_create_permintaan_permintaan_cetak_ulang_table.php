<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permintaan_cetak_ulang', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat')->unique(); // ex: 0001/UML/VI/2026
            $table->date('tanggal_permohonan');

            // Data pemohon (sesuai form fisik: Nama, Jabatan, Bagian/Seksi)
            $table->string('nama_pemohon');
            $table->string('jabatan_pemohon')->nullable();
            $table->foreignId('bagian_id')
                ->nullable()
                ->constrained('bagian_petugas')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            // Barcode yang diminta cetak ulang
            $table->foreignId('pendataan_kantong_id')
                ->constrained('pendataan_kantong')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->integer('jumlah_cetak')->default(1);

            $table->text('alasan');

            // Workflow approval
            $table->string('status')->default('diajukan'); // diajukan, disetujui, ditolak, selesai
            $table->string('nama_petugas_melayani')->nullable();
            $table->string('nama_kasi')->nullable();
            $table->date('tgl_disetujui')->nullable();
            $table->text('catatan')->nullable();

            $table->string('user_input')->nullable();
            $table->string('user_proses')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_cetak_ulang');
    }
};