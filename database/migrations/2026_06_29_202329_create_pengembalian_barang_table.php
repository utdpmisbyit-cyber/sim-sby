<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengembalian_barang', function (Blueprint $table) {
            $table->id();
            $table->string('no_kembali', 20)->index()->comment('Format: PBYYMMxxxxxx');
            $table->date('tgl_kembali');

            // Asal pengembalian barang: user/departemen yang mengembalikan
            // barang ke gudang (BUKAN alur pinjam-kembali, jadi tidak
            // relasi ke pinjam_barang).
            $table->string('departemen', 150)->nullable()
                  ->comment('Nama user/departemen yang mengembalikan barang');

            $table->string('keterangan', 255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['no_kembali', 'tgl_kembali']);
        });
         Schema::create('pengembalian_barang_detail', function (Blueprint $table) {
            $table->id();
 
            $table->foreignId('pengembalian_barang_id')
                  ->constrained('pengembalian_barang')
                  ->cascadeOnDelete();
 
            $table->foreignId('barang_id')->constrained('barang');
 
            // Diisi hanya jika barang bertipe/berjenis "Kantong Darah" atau
            // barang lain yang dilacak per nomor fisik. Boleh kosong untuk
            // barang biasa (buku, kertas, dll).
            $table->string('no_kantong', 100)->nullable()->index();
 
            $table->integer('jumlah');
            $table->enum('kondisi', ['baik', 'rusak'])->default('baik');
 
            // Jejak ke tabel stok (ledger qty_in) yang dibuat OTOMATIS saat
            // baris ini dikonfirmasi dengan kondisi = 'baik'. Untuk kondisi
            // 'rusak', kolom ini NULL karena tidak menambah stok pakai.
            $table->string('no_trans_stok', 50)->nullable()->index();
 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengembalian_barang_detail');
        Schema::dropIfExists('pengembalian_barang');
    }
};