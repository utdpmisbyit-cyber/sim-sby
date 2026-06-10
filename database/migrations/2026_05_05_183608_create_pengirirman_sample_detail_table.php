<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengiriman_sample_detail', function (Blueprint $table) {
            $table->id();
            
            // Menghubungkan ke tabel header
            $table->foreignId('pengiriman_sample_id')
                  ->constrained('pengiriman_sample')
                  ->onDelete('cascade');

            $table->integer('urut')->nullable();
            $table->string('no_kantong')->nullable(); // Sesuai image_f3dc20.png
            $table->string('jenis_kantong')->nullable();
            $table->unsignedBigInteger('aftap_id')->nullable();
            $table->dateTime('tanggal_aftap')->nullable();
            $table->unsignedBigInteger('donor_id')->nullable();
            $table->string('no_donor')->nullable();
            $table->string('nama_donor')->nullable();
            $table->unsignedBigInteger('asal_darah_id')->nullable();
            $table->string('kode_asal_darah')->nullable();
            $table->string('gol_darah', 5)->nullable();
            $table->string('rhesus', 20)->nullable();
            $table->boolean('tolak')->default(false);
            $table->string('keterangan')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('petugas_id')->nullable();
            $table->unsignedBigInteger('cabang_id')->nullable();
            $table->string('perkiraan')->nullable();
            $table->string('jenis_donor')->nullable();
            
            // Kolom ini biasanya ikut header, tapi jika tiap kantong punya suhu sendiri:
            $table->string('suhu')->nullable();
            $table->string('id_logger')->nullable();
            $table->string('id_coolbox')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengiriman_sample_detail');
    }
};