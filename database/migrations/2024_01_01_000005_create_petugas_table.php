<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petugas', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->text('alamat_1')->nullable();
            $table->text('alamat_2')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('no_telp')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('cabang_id')->constrained('cabang')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('jabatan_id')->nullable()->constrained('jabatan')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('bagian_id')->nullable()->constrained('bagian_petugas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('program_kerja_id')->nullable()->constrained('program_kerja')->cascadeOnUpdate()->nullOnDelete();
            $table->string('tanda_tangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('program_kerja', function (Blueprint $table) {
            $table->foreignId('pic_id')->nullable()->change();
            $table->foreign('pic_id')->references('id')->on('petugas')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('program_kerja', function (Blueprint $table) {
            $table->dropForeign(['pic_id']);
        });
        Schema::dropIfExists('petugas');
    }
};
