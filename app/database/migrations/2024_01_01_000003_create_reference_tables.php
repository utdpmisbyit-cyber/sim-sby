<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wilayah', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('kecamatan', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->foreignId('wilayah_id')->constrained('wilayah')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('kewarganegaraan', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pekerjaan', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('jabatan', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('bagian_petugas', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cabang', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->text('alamat_1')->nullable();
            $table->text('alamat_2')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('no_telp')->nullable();
            $table->string('jenis');
            $table->boolean('status');
            $table->timestamps();
            $table->softDeletes();
        });



    }

    public function down(): void
    {
        Schema::dropIfExists('cabang');
        Schema::dropIfExists('bagian_petugas');
        Schema::dropIfExists('jabatan');
        Schema::dropIfExists('pekerjaan');
        Schema::dropIfExists('kewarganegaraan');
        Schema::dropIfExists('kecamatan');
        Schema::dropIfExists('wilayah');
    }
};
