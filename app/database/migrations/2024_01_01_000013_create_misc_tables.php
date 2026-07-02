<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelompok_rumah_sakit', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('bagian_rumah_sakit', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('jenis_darah', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->string('nama_pendek');
            $table->integer('umur_darah');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('bank_darah', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->text('alamat');
            $table->string('jenis');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('kelas_tujuan_darah', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tujuan_darah', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('kelompok_rumah_sakit_id')->constrained('kelompok_rumah_sakit')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('diagnosa', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('biaya_cross_test', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->integer('harga');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('jenis_biaya', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('kelompok_biaya', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('service_cost', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('jenis');
            $table->integer('biaya');
            $table->foreignId('jenis_biaya_id')->constrained('jenis_biaya')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('kelompok_biaya_id')->constrained('kelompok_biaya')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('mobil_unit', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('merk_mobil');
            $table->string('no_polisi')->unique();
            $table->integer('tahun_produksi');
            $table->integer('tahun_beli');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pasien_polisitemi', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->text('alamat');
            $table->string('kode_pos');
            $table->timestamp('tanggal_lahir');
            $table->string('jenis_kelamin');
            $table->string('agama');
            $table->string('no_telp');
            $table->string('no_ktp');
            $table->string('golongan_darah');
            $table->string('rhesus');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('kelompok_barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('supplier', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->text('alamat');
            $table->string('no_telp');
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->string('satuan');
            $table->integer('stok');
            $table->integer('harga_satuan');
            $table->integer('min_stok');
            $table->string('jenis_barang');
            $table->foreignId('cabang_id')->constrained('cabang')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
        Schema::dropIfExists('supplier');
        Schema::dropIfExists('kelompok_barang');
        Schema::dropIfExists('pasien_polisitemi');
        Schema::dropIfExists('mobil_unit');
        Schema::dropIfExists('service_cost');
        Schema::dropIfExists('kelompok_biaya');
        Schema::dropIfExists('jenis_biaya');
        Schema::dropIfExists('biaya_cross_test');
        Schema::dropIfExists('diagnosa');
        Schema::dropIfExists('tujuan_darah');
        Schema::dropIfExists('kelas_tujuan_darah');
        Schema::dropIfExists('bank_darah');
        Schema::dropIfExists('jenis_darah');
        Schema::dropIfExists('bagian_rumah_sakit');
        Schema::dropIfExists('kelompok_rumah_sakit');
    }
};
