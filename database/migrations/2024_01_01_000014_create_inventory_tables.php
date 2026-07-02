<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggaran', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->timestamp('tgl_input');
            $table->string('tahun_anggaran', 4);
            $table->text('keterangan');
            $table->integer('nilai_anggaran');
            $table->string('user_input');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pengajuan_supplier', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->timestamp('tgl_pengajuan')->nullable();
            $table->string('jenis_pengajuan');
            $table->integer('status');
            $table->timestamp('tgl_evaluasi')->nullable();
            $table->string('user_input');
            $table->string('user_proses');
            $table->foreignId('supplier_id')->constrained('supplier')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('dokumentasi', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->text('uri_text');
            $table->foreignId('pengajuan_supplier_id')->unique()->constrained('pengajuan_supplier')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('pengajuan_barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->timestamp('tgl_pengajuan')->nullable();
            $table->string('jenis_pengajuan');
            $table->string('status');
            $table->string('user_input');
            $table->string('user_proses');
            $table->string('bagian_id');
            $table->foreignId('cabang_id')->constrained('cabang')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('petugas_id')->constrained('petugas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('nama_barang');
            $table->string('satuan');
            $table->integer('jml_minta');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('dokumentasi_barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->text('uri_text');
            $table->foreignId('pengajuan_barang_id')->unique()->constrained('pengajuan_barang')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
            // No softdeletes in original schema for DokumentasiBarang
        });

        Schema::create('pemakaian_barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->timestamp('tgl_pemakaian');
            $table->integer('jumlah_pakai');
            $table->text('keterangan')->nullable();
            $table->foreignId('pengajuan_barang_id')->constrained('pengajuan_barang')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('nama_barang');
            $table->string('user_input');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('stok', function (Blueprint $table) {
            $table->id();
            $table->string('no_trans_stok')->unique();
            $table->timestamp('tgl_proses');
            $table->integer('proses');
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('qty_in');
            $table->integer('qty_out');
            $table->integer('harga');
            $table->text('keterangan');
            $table->integer('aktif');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pengeluaran_barang', function (Blueprint $table) {
            $table->id();
            $table->string('no_trans_keluar')->unique();
            $table->timestamp('tgl_keluar');
            $table->string('user_input');
            $table->string('user_proses');
            $table->foreignId('pengajuan_barang_id')->constrained('pengajuan_barang')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('nama_barang');
            $table->string('no_lot')->nullable();
            $table->timestamp('tgl_expired')->nullable();
            $table->string('status')->nullable();
            $table->integer('qty_keluar');
            $table->string('satuan');
            $table->text('keterangan')->nullable();
            $table->foreignId('stok_id')->nullable()->constrained('stok')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('opname_barang', function (Blueprint $table) {
            $table->id();
            $table->string('no_opname')->unique();
            $table->timestamp('tgl_opname');
            $table->string('status');
            $table->foreignId('petugas_id')->constrained('petugas')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('user_input');
            $table->string('user_proses')->nullable();
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('nama_barang');
            $table->integer('qty_sistem');
            $table->integer('qty_fisik');
            $table->integer('selisih');
            $table->string('satuan');
            $table->text('keterangan')->nullable();
            $table->string('lokasi');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('purchase_order', function (Blueprint $table) {
            $table->id();
            $table->string('no_po')->unique();
            $table->timestamp('tgl_po');
            $table->integer('status_po');
            $table->integer('total_po');
            $table->string('app_po');
            $table->foreignId('supplier_id')->constrained('supplier')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('user_input');
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('anggaran_id')->constrained('anggaran')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('purchase_order_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_order')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('qty_po');
            $table->integer('harga_po');
            $table->integer('subtotal_po');
            $table->timestamps();
        });

        Schema::create('qc_barang_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('no_trans_qc')->unique();
            $table->timestamp('tgl_qc')->nullable();
            $table->timestamp('tgl_beli')->nullable();
            $table->integer('status_qc');
            $table->string('no_faktur');
            $table->foreignId('purchase_order_id')->constrained('purchase_order')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('supplier')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('user_proses');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('qc_detail_lot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qc_barang_masuk_id')->constrained('qc_barang_masuk')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('no_lot');
            $table->string('jenis_barang');
            $table->integer('qty_terima');
            $table->integer('harga');
            $table->integer('subtotal_harga');
            $table->timestamp('tgl_exp_date')->nullable();
            $table->integer('suhu')->nullable();
            $table->timestamps();
        });

        Schema::create('so', function (Blueprint $table) {
            $table->id();
            $table->string('no_trans_so')->unique();
            $table->timestamp('tgl_so');
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('qty_so');
            $table->integer('harga_so');
            $table->foreignId('qc_barang_masuk_id')->unique()->constrained('qc_barang_masuk')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('return_supplier', function (Blueprint $table) {
            $table->id();
            $table->string('no_trans_retur')->unique();
            $table->timestamp('tgl_retur');
            $table->foreignId('supplier_id')->constrained('supplier')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('jml_retur');
            $table->string('satuan');
            $table->string('jenis_retur');
            $table->integer('total_retur');
            $table->string('user_input');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('return_supplier_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_supplier_id')->constrained('return_supplier')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('qty_retur');
            $table->integer('harga_retur');
            $table->integer('subtotal_retur');
            $table->timestamps();
        });

        Schema::create('permintaan_supplier', function (Blueprint $table) {
            $table->id();
            $table->string('no_permintaan')->unique();
            $table->timestamp('tgl_permintaan');
            $table->foreignId('supplier_id')->constrained('supplier')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('qty');
            $table->string('satuan');
            $table->string('status')->default('DRAFT');
            $table->text('keterangan')->nullable();
            $table->string('user_input');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pinjam_barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('petugas_id')->constrained('petugas')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('jumlah_pinjam');
            $table->foreignId('bagian_id')->constrained('bagian_petugas')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamp('tanggal_pinjam')->useCurrent();
            $table->text('keterangan')->nullable();
            $table->string('diserahkan_ke');
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('retur_pinjam', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('pinjam_barang_id')->constrained('pinjam_barang')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('petugas_id')->constrained('petugas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('bagian_petugas_id')->nullable()->constrained('bagian_petugas')->cascadeOnUpdate()->nullOnDelete();
            $table->integer('jumlah_retur');
            $table->timestamp('tanggal_retur')->useCurrent();
            $table->text('kondisi_barang')->nullable();
            $table->foreignId('return_supplier_id')->nullable()->constrained('return_supplier')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retur_pinjam');
        Schema::dropIfExists('pinjam_barang');
        Schema::dropIfExists('permintaan_supplier');
        Schema::dropIfExists('return_supplier_detail');
        Schema::dropIfExists('return_supplier');
        Schema::dropIfExists('so');
        Schema::dropIfExists('qc_detail_lot');
        Schema::dropIfExists('qc_barang_masuk');
        Schema::dropIfExists('purchase_order_detail');
        Schema::dropIfExists('purchase_order');
        Schema::dropIfExists('opname_barang');
        Schema::dropIfExists('pengeluaran_barang');
        Schema::dropIfExists('stok');
        Schema::dropIfExists('pemakaian_barang');
        Schema::dropIfExists('dokumentasi_barang');
        Schema::dropIfExists('pengajuan_barang');
        Schema::dropIfExists('dokumentasi');
        Schema::dropIfExists('pengajuan_supplier');
        Schema::dropIfExists('anggaran');
    }
};
