<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penyisihan_kantong_aftap', function (Blueprint $table) {
            $table->id();
            // ditampilkan sbg "Nomor Penyisihan" di form, digenerate otomatis
            $table->string('no_transaksi', 30)->unique();
            // ditampilkan sbg "Tanggal Penyisihan" di form
            $table->date('tanggal');

            $table->text('keterangan')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('penyisihan_kantong_aftap_detail', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('penyisihan_id')->index();

            // referensi langsung ke detail penerimaan kantong (stok_kantong_penerimaan_detail)
            $table->unsignedBigInteger('penerimaan_detail_id')->nullable()->index();

            $table->string('no_kantong', 100)->index();
            $table->string('no_lot', 50)->nullable();
            $table->string('merk', 100)->nullable();
            $table->string('jenis', 100)->nullable();
            $table->string('ukuran', 50)->nullable();

            // snapshot data darah/kantong pada saat discan, untuk ditampilkan di grid
            $table->string('gol_darah', 5)->nullable();
            $table->string('rhesus', 10)->nullable();
            $table->date('tgl_aftap')->nullable();
            $table->date('tgl_kadaluarsa')->nullable();
            $table->string('status', 30)->nullable();

            // alasan penyisihan PER KANTONG, mis: "Darah Tidak Terserap", "Uji Mutu",
            // "Keruh", "DCT Positif / Mayor Positif", "Expired Date", "Bocor", "HBc Positif"
            $table->string('alasan', 100)->nullable();

            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->foreign('penyisihan_id')
                  ->references('id')->on('penyisihan_kantong_aftap')
                  ->cascadeOnDelete();

            $table->foreign('penerimaan_detail_id')
                  ->references('id')->on('stok_kantong_penerimaan_detail')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyisihan_kantong_aftap_detail');
        Schema::dropIfExists('penyisihan_kantong_aftap');
    }
};