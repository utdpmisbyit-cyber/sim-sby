<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        
        Schema::create('pelayanan_darah', function (Blueprint $table) {
            $table->id();
            $table->string('no_pelayanan', 30)->unique();

            $table->unsignedBigInteger('pemberian_darah_id')->nullable();
            $table->string('no_pemberian', 30)->nullable()->index();
            $table->string('no_fpup', 30)->nullable()->index();
            $table->date('tgl_fpup');

            // Waktu
            $table->date('tgl_pelayanan');
            $table->time('jam_pelayanan')->nullable();

            $table->string('cara_bayar', 20)->nullable();
            $table->string('jns_biaya', 100)->nullable();
            $table->string('no_register', 30)->nullable();
            $table->string('no_faktur', 50)->nullable();

            $table->string('nama_pasien', 100)->nullable();
            $table->string('nama_dokter', 100)->nullable();
            $table->string('nama_rs', 100)->nullable();
            $table->string('kode_rs', 20)->nullable();
            $table->string('jenis_rs', 50)->nullable();
            $table->string('bagian_rs', 100)->nullable();
            $table->string('kelas_rawat', 50)->nullable();
            $table->string('golongan_darah', 10)->nullable();
            $table->string('rhesus', 10)->nullable();
            $table->string('alamat_os', 200)->nullable();

            $table->decimal('total_biaya', 14, 2)->default(0);
            $table->decimal('diskon', 14, 2)->default(0);
            $table->decimal('total_bayar', 14, 2)->default(0);
            $table->decimal('terbayar', 14, 2)->default(0);
            $table->decimal('kembalian', 14, 2)->default(0);

            // Status & Petugas
            $table->string('status', 20)->default('baru');
            $table->string('petugas_kasir', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('cara_pembayaran', 30)->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        
        Schema::create('pelayanan_darah_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pelayanan_darah_id');
            $table->unsignedBigInteger('pemberian_darah_detail_id')->nullable();

            $table->string('no_stok', 50)->nullable();
            $table->string('jns_darah', 50)->nullable();
            $table->string('gol', 5)->nullable();
            $table->string('rhesus', 10)->nullable();
            $table->integer('jumlah')->default(1);
            $table->integer('cc')->nullable();
            $table->decimal('harga_satuan', 12, 2)->default(0);
            $table->decimal('total_harga', 12, 2)->default(0);
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });

        Schema::table('pelayanan_darah', function (Blueprint $table) {
            // Hanya tambah FK jika tabel pemberian_darah sudah ada
            if (Schema::hasTable('pemberian_darah')) {
                $table->foreign('pemberian_darah_id')
                      ->references('id')
                      ->on('pemberian_darah')
                      ->nullOnDelete();
            }
        });

        Schema::table('pelayanan_darah_detail', function (Blueprint $table) {
            $table->foreign('pelayanan_darah_id')
                  ->references('id')
                  ->on('pelayanan_darah')
                  ->onDelete('cascade');

            if (Schema::hasTable('pemberian_darah_detail')) {
                $table->foreign('pemberian_darah_detail_id')
                      ->references('id')
                      ->on('pemberian_darah_detail')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        
        Schema::table('pelayanan_darah_detail', function (Blueprint $table) {
            $table->dropForeign(['pelayanan_darah_id']);

            if (Schema::hasTable('pemberian_darah_detail')) {
                $table->dropForeign(['pemberian_darah_detail_id']);
            }
        });

        Schema::table('pelayanan_darah', function (Blueprint $table) {
            if (Schema::hasTable('pemberian_darah')) {
                $table->dropForeign(['pemberian_darah_id']);
            }
        });

        Schema::dropIfExists('pelayanan_darah_detail');
        Schema::dropIfExists('pelayanan_darah');
    }
};