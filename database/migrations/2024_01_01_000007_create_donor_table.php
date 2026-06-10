<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donor', function (Blueprint $table) {
             $table->engine = 'InnoDB';
            $table->id();
            $table->string('kode')->unique();
            $table->string('no_pendaftaran')->unique();
            $table->string('nama');
            $table->text('alamat_1')->nullable();
            $table->text('alamat_2')->nullable();
            $table->string('kode_pos')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->integer('usia')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->foreignId('kewarganegaraan_id')->constrained('kewarganegaraan')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('wilayah_id')->constrained('wilayah')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('kecamatan_id')->constrained('kecamatan')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('pekerjaan_id')->constrained('pekerjaan')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('agama')->nullable();
            $table->string('no_telp')->nullable();
            $table->string('no_ktp')->nullable();
            $table->string('no_sim')->nullable();
            $table->string('golongan_darah')->nullable();
            $table->string('rhesus')->nullable();
            $table->string('golongan_darah_lain')->nullable();
            $table->string('golongan_rhesus')->nullable();
            $table->text('cekal')->nullable();
            $table->timestamp('tanggal_cekal')->nullable();
            $table->integer('penghargaan')->nullable();
            $table->string('no_cekal')->nullable()->unique();
            $table->integer('donor_ke')->default(1);
            $table->string('skrining')->nullable();
             $table->unsignedBigInteger('fpup_id')->nullable()->constrained('fpup')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('no_fpup')->nullable();
            $table->unsignedInteger('asal_darah_id')->nullable()->constrained('asal_darah')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('nama_asal_darah')->nullable();
            $table->integer('counter_cekal')->default(0);
            $table->boolean('is_golongan_darah_locked')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donor');
    }
};
