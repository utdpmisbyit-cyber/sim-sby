<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asal_darah', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->text('alamat_1')->nullable();
            $table->text('alamat_2')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('no_telp')->nullable();
            $table->string('nama_sponsor')->nullable();
            $table->string('no_telp_sponsor')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('kantong_darah', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->integer('stok');
            $table->integer('stok_produksi')->default(0);
            $table->integer('stok_hasil_produksi')->default(0);
            $table->integer('stok_aftap')->default(0);
            $table->integer('min_stok')->nullable();
            $table->foreignId('cabang_id')->constrained('cabang')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('duplikat_cetak')->nullable();
            $table->integer('harga_satuan')->nullable();
            $table->string('merk')->nullable();
            $table->string('tipe_jenis_kantong')->nullable();
            $table->string('jenis_kantong')->nullable();
            $table->string('ukuran_kantong')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('produksi_kantong_darah', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('kantong_darah_id')->constrained('kantong_darah')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('nomor_lot');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('kantong_darah_hasil_produksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('kantong_darah_id')->constrained('kantong_darah')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('log_donor_id')->nullable()->unique()->constrained('log_donor')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('produksi_kantong_darah_id')->unique()->constrained('produksi_kantong_darah')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('pemilik')->default('Gudang');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('permintaan_aftap', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('status')->default('PENDING');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('kantong_darah_permintaan_aftap', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permintaan_aftap_id')->constrained('permintaan_aftap')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('kantong_darah_id')->constrained('kantong_darah')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('jumlah');
        });
        Schema::create('pendataan_kantong', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('barcode', 50)->nullable();
            $table->string('merk_kantong', 50)->nullable();
            $table->string('jenis_kantong', 50)->nullable();
            $table->string('type_kantong', 20)->nullable();
            $table->string('ukuran', 20)->nullable();
            $table->string('no_lot', 30)->nullable();
            $table->unsignedTinyInteger('duplikat')->default(1);
            $table->string('status', 30)->default('aktif');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('stok_kantong_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('no_terima', 20)->index()->comment('Nomor penerimaan, format: YYMMxxxxxx');
            $table->date('tgl_terima');
            $table->string('no_kantong', 100)->index();
            $table->string('no_lot', 50)->nullable();
            $table->string('merk', 100)->nullable();
            $table->string('jenis', 100)->nullable();
            $table->string('tipe', 100)->nullable();
            $table->string('ukuran', 50)->nullable();
            $table->enum('status', ['tersedia', 'keluar', 'rusak'])->default('tersedia');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['no_terima', 'tgl_terima']);
        });

         Schema::create('stok_kantong_keluar', function (Blueprint $table) {
            $table->id();
            $table->string('no_keluar', 20)->index();
            $table->date('tgl_keluar');
            $table->string('no_kantong', 100)->index();
            $table->string('no_lot', 50)->nullable();
            $table->string('merk', 100)->nullable();
            $table->string('jenis', 100)->nullable();
            $table->string('tipe', 100)->nullable();
            $table->string('ukuran', 50)->nullable();
            $table->string('tujuan', 150)->nullable();
            $table->text('keterangan')->nullable();
            
            $table->unsignedBigInteger('detail_id')->nullable();
            $table->unsignedBigInteger('permintaan_kantong_id')->nullable();
 
            
            $table->unsignedBigInteger('created_by')->nullable();
           
            $table->index('detail_id');
            $table->index('permintaan_kantong_id');
           
            $table->timestamps();
            $table->softDeletes();
            $table->index(['no_keluar', 'tgl_keluar']);
        });
    }   

    public function down(): void
    {
        Schema::dropIfExists('pendataan_kantong');
        Schema::dropIfExists('stok_kantong_masuk');
        Schema::dropIfExists('stok_kantong_keluar');
        
        Schema::dropIfExists('kantong_darah_permintaan_aftap');
        Schema::dropIfExists('permintaan_aftap');
        Schema::dropIfExists('kantong_darah_hasil_produksi');
        Schema::dropIfExists('produksi_kantong_darah');
        Schema::dropIfExists('kantong_darah');
        Schema::dropIfExists('asal_darah');
    }
};
