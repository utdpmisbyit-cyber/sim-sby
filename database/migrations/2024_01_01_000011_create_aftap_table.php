<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aftap', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('log_donor_id')->unique()->constrained('log_donor')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('dokter_id')->nullable()->constrained('petugas')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('donor_id')->constrained('donor')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('asal_darah_id')->nullable()->constrained('asal_darah')->cascadeOnUpdate()->nullOnDelete();
            $table->string('status')->default('Pending');
            $table->tinyInteger('bed')->nullable();
            $table->text('lengan')->nullable();
            $table->text('alamat_surat')->nullable();
            $table->boolean('bersedia_dikirim_surat')->nullable();
            $table->text('cara_ambil')->nullable();
            $table->boolean('cuci_tangan')->nullable();
            $table->boolean('darah_lancar')->nullable();
            $table->boolean('donor_sewaktu_waktu')->nullable();
            $table->integer('id_hemoscale')->nullable();
            $table->timestamp('jam_mulai')->nullable();
            $table->timestamp('jam_selesai')->nullable();
            $table->text('jenis_donor')->nullable();
            $table->text('kantong_penuh')->nullable();
            $table->text('cc_ambil')->nullable();
            $table->text('satelit')->nullable();
            $table->text('durasi')->nullable();
            $table->text('keterangan')->nullable();
            $table->text('lain_lain')->nullable();
            $table->string('no_kantong')->nullable();
            $table->string('no_selang')->nullable();
            $table->boolean('penusukan_sulit')->nullable();
            $table->text('reaksi_donor')->nullable();
            $table->boolean('sample_darah')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aftap');
    }
};
