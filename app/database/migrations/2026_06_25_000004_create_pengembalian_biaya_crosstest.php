<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengembalian_biaya_crosstest', function (Blueprint $table) {
            $table->id();

            // Referensi ke FPUP asal (sumber data ketika di-scan)
            $table->unsignedBigInteger('permintaan_fpup_id')->nullable();
            $table->string('no_retur', 30)->unique();
            $table->string('no_fpup', 30);
            $table->date('tgl_fpup')->nullable();
            $table->string('no_reg', 20)->nullable();

            // Snapshot data RS & Pasien (diambil saat scan, supaya histori tidak berubah
            // walau data FPUP aslinya diedit/dihapus di kemudian hari)
            $table->string('kode_rs', 20)->nullable();
            $table->string('nama_rs', 100)->nullable();
            $table->string('jenis_rs', 50)->nullable();
            $table->string('kategori_rs', 50)->nullable();
            $table->string('bagian', 50)->nullable();
            $table->string('kelas_rawat', 50)->nullable();
            $table->string('nama_pasien', 100)->nullable();
            $table->string('no_ktp', 20)->nullable();
            $table->string('nama_dokter', 100)->nullable();

            // Dasar perhitungan biaya (jenis_biaya & service_cost yang dipakai)
            $table->string('jns_biaya', 100)->nullable();
            $table->unsignedBigInteger('jenis_biaya_id')->nullable();
            $table->string('kode_service_cost', 30)->nullable();

            // Nilai
            $table->decimal('sub_total', 15, 2)->default(0);    // hasil hitung sistem (jumlah x harga)
            $table->decimal('total_retur', 15, 2)->default(0);  // nilai final, bisa disesuaikan kasir

            // Info transaksi retur
            $table->dateTime('tgl_retur')->nullable();
            $table->string('kode_kasir', 20)->nullable();
            $table->string('nama_kasir', 100)->nullable();
            $table->string('no_nota', 30)->nullable();
            $table->text('keterangan')->nullable();

            $table->string('status', 20)->default('baru'); // baru, disimpan, batal
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index('no_fpup');

            $table->foreign('permintaan_fpup_id', 'fk_pbc_fpup')
                  ->references('id')->on('permintaan_fpup')
                  ->onDelete('set null');
        });

        // Detail item, merepresentasikan "Tabel Penagihan Biaya" pada form lama
        Schema::create('pengembalian_biaya_crosstest_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pengembalian_biaya_crosstest_id');
            $table->unsignedBigInteger('permintaan_fpup_detail_id')->nullable();

            // Kolom mengikuti grid: NAMAOS, NoMinta, CKDRS, NamaRS, jenisRS, BagianRawat
            $table->string('nama_os', 100)->nullable();
            $table->string('no_minta', 30)->nullable();
            $table->string('kode_rs', 20)->nullable();
            $table->string('nama_rs', 100)->nullable();
            $table->string('jenis_rs', 50)->nullable();
            $table->string('bagian_rawat', 50)->nullable();

            // Detail darah yang diminta (sumber jumlah)
            $table->string('jns_darah', 50)->nullable();
            $table->string('gol_darah', 5)->nullable();
            $table->string('rhesus', 10)->nullable();
            $table->integer('jumlah')->default(1);
            $table->integer('cc')->nullable();

            // Perhitungan biaya per item (sumber harga: service_cost)
            $table->string('kode_service_cost', 30)->nullable();
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);

            $table->timestamps();

            $table->foreign('pengembalian_biaya_crosstest_id', 'fk_pbc_detail_header')
                  ->references('id')->on('pengembalian_biaya_crosstest')
                  ->onDelete('cascade');

            $table->foreign('permintaan_fpup_detail_id', 'fk_pbc_detail_fpupdetail')
                  ->references('id')->on('permintaan_fpup_detail')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengembalian_biaya_crosstest_detail');
        Schema::dropIfExists('pengembalian_biaya_crosstest');
    }
};