<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemeriksaan_dokter', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('log_donor_id')->unique()->constrained('log_donor')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('dokter_id')->nullable()->constrained('petugas')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('donor_id')->constrained('donor')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('status')->default('Pending');
            $table->boolean('puasa')->nullable();
            $table->text('alasan')->nullable();
            $table->double('berat_badan')->nullable();
            $table->text('data_kuisioner')->nullable();
            $table->integer('diastole')->nullable();
            $table->text('ecg')->nullable();
            $table->text('jenis_kantong')->nullable();
            $table->text('keterangan')->nullable();
            $table->text('cc_ambil')->nullable();
            $table->integer('nadi')->nullable();
            $table->integer('nomor_ruangan')->nullable();
            $table->text('sampling')->nullable();
            $table->integer('sistole')->nullable();
            $table->double('suhu')->nullable();
            $table->double('tinggi_badan')->nullable();
            $table->text('tipe_jenis_kantong')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pemeriksaan_hb', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('log_donor_id')->unique()->constrained('log_donor')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('dokter_id')->nullable()->constrained('petugas')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('donor_id')->constrained('donor')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('status')->default('Pending');
            $table->text('alasan_ditolak')->nullable();
            $table->integer('eritrosit')->nullable();
            $table->string('golongan_darah')->nullable();
            $table->double('hb_meter')->nullable();
            $table->integer('hematocrit')->nullable();
            $table->integer('lecosit')->nullable();
            $table->text('lengan')->nullable();
            $table->text('metode')->nullable();
            $table->string('rhesus')->nullable();
            $table->text('sampling')->nullable();
            $table->text('screening')->nullable();
            $table->integer('trombosit')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pemeriksaan_konseling', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('log_donor_id')->unique()->constrained('log_donor')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('donor_id')->constrained('donor')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('konselor_id')->nullable()->constrained('petugas')->cascadeOnUpdate()->nullOnDelete();
            $table->string('status')->default('Pending');
            $table->text('catatan')->nullable();
            $table->boolean('cekal')->nullable();
            $table->text('hasil_pantau')->nullable();
            $table->text('jenis_periksa')->nullable();
            $table->text('kesimpulan')->nullable();
            $table->double('nilai_cov')->nullable();
            $table->double('nilai_od')->nullable();
            $table->text('status_periksa')->nullable();
            $table->timestamp('tanggal_aftap')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_konseling');
        Schema::dropIfExists('pemeriksaan_hb');
        Schema::dropIfExists('pemeriksaan_dokter');
    }
};
