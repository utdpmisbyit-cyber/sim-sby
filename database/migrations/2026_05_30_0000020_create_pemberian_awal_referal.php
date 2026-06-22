<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemberian_awal_referal', function (Blueprint $table) {
            $table->id();
            $table->string('no_pemberian', 30)->unique();

            // Relasi ke permintaan FPUP (lihat modul permintaan_fpup)
            $table->unsignedBigInteger('fpup_id')->nullable();
            $table->string('no_fpup', 30)->nullable();
            $table->dateTime('tgl_fpup')->nullable();
            $table->string('nofpup_dari_cm', 30)->nullable();

            // Cara bayar & identifikasi antibodi
            $table->enum('cara_bayar', ['langsung_tunai', 'kredit'])->default('langsung_tunai');
            $table->boolean('identifikasi_antibodi')->default(false);

            // Data pasien (didenormalisasi agar histori tidak berubah jika master pasien diubah)
            $table->unsignedBigInteger('pasien_id')->nullable();
            $table->string('nama_pasien');
            $table->string('noktp_pasien', 20)->nullable();
            $table->enum('jenis_kelamin', ['pria', 'wanita']);
            $table->text('alamat_pasien')->nullable();

            // Data rumah sakit perujuk
            $table->string('kode_rs', 20)->nullable();
            $table->string('nama_rs')->nullable();
            $table->string('no_reg', 30)->nullable();

            // Golongan darah & seleksi
            $table->string('gol_darah', 3);
            $table->enum('rhesus', ['Positif', 'Negatif'])->default('Positif');
            $table->boolean('pasien_karier')->default(false);
            $table->unsignedInteger('seleksi')->default(1);

            $table->json('stocks')->nullable();
            $table->json('biaya_lain')->nullable();

            // Nilai ringkasan, dihitung ulang dari kolom JSON di atas setiap kali simpan
            $table->unsignedInteger('jumlah_kantong_per_seleksi')->default(0);
            $table->decimal('total_biaya', 15, 2)->default(0);

            $table->enum('status', ['draft', 'diproses', 'selesai', 'dibatalkan'])->default('draft');
            $table->text('catatan')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('no_fpup');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemberian_awal_referal');
    }
};