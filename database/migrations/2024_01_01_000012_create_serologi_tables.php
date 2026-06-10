<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengiriman_serologi', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('pengirim_id')->constrained('petugas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('penerima_id')->nullable()->constrained('petugas')->cascadeOnUpdate()->nullOnDelete();
            $table->text('dokumen')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pengiriman_serologi_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pengiriman_serologi_id');
            $table->string('no_kantong')->nullable();
            $table->string('no_selang')->nullable();
            $table->string('jenis_kantong')->nullable();
            $table->string('no_donor')->nullable();
            $table->string('nama_donor')->nullable();
            $table->string('gol_darah', 5)->nullable();
            $table->string('rhesus', 5)->nullable();
            $table->string('asal_darah')->nullable();
            $table->datetime('tanggal_aftap')->nullable();
            $table->boolean('tolak')->default(false);
            $table->boolean('is_nat')->default(false);
            $table->timestamps();
 
            $table->foreign('pengiriman_serologi_id')
                  ->references('id')
                  ->on('pengiriman_serologi')
                  ->onDelete('cascade');
        });

        Schema::create('pengiriman_produksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('pengirim_id')->constrained('petugas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('penerima_id')->nullable()->constrained('petugas')->cascadeOnUpdate()->nullOnDelete();
            $table->text('dokumen')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pemeriksaan_sampel', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('status')->default('SENDING');
            $table->string('barcode');
            $table->foreignId('pengiriman_serologi_id')->nullable()->constrained('pengiriman_serologi')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('produksi_darah', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('barcode');
            $table->string('status')->default('SENDING');
            $table->foreignId('pengiriman_produksi_id')->nullable()->constrained('pengiriman_produksi')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('plate_serologi', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('metode_serologi', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('reagen_serologi', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('worksheet_umum_serologi', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('jenis_periksa');
            $table->foreignId('plate_serologi_id')->constrained('plate_serologi')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('metode_serologi_id')->constrained('metode_serologi')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('reagen_serologi_id')->constrained('reagen_serologi')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('plate_mapping_serologi', function (Blueprint $table) {
            $table->id();
            $table->string('address');
            $table->string('barcode');
            $table->foreignId('plate_serologi_id')->constrained('plate_serologi')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::create('mapping_serologi_result', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plate_mapping_serologi_id')->constrained('plate_mapping_serologi')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('hasil_tahapan');
            $table->string('kesimpulan');
            $table->foreignId('worksheet_umum_serologi_id')->constrained('worksheet_umum_serologi')->cascadeOnUpdate()->restrictOnDelete();
            $table->double('hasil_cov');
            $table->double('hasil_od');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mapping_serologi_result');
        Schema::dropIfExists('plate_mapping_serologi');
        Schema::dropIfExists('worksheet_umum_serologi');
        Schema::dropIfExists('reagen_serologi');
        Schema::dropIfExists('metode_serologi');
        Schema::dropIfExists('plate_serologi');
        Schema::dropIfExists('produksi_darah');
        Schema::dropIfExists('pemeriksaan_sampel');
        Schema::dropIfExists('pengiriman_produksi');
        Schema::dropIfExists('pengiriman_serologi');
        Schema::dropIfExists('pengiriman_serologi_detail');
    }
};
