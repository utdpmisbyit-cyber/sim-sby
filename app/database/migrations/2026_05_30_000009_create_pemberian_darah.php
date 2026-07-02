<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemberian_darah', function (Blueprint $table) {
            $table->id();
            $table->string('no_pemberian', 30)->unique();
            $table->string('no_fpup', 30)->nullable()->index();
            $table->unsignedBigInteger('permintaan_fpup_id')->nullable();

            $table->date('tgl_keluar');
            $table->time('jam_keluar')->nullable();

            $table->string('nama_penerima', 100)->nullable();
            $table->string('alamat_penerima', 200)->nullable();

            $table->string('nama_pasien', 100)->nullable();
            $table->string('nama_dokter', 100)->nullable();
            $table->string('nama_rs', 100)->nullable();
            $table->string('kode_rs', 20)->nullable();
            $table->string('jenis_rs', 50)->nullable();
            $table->string('kelas_rawat', 50)->nullable();
            $table->string('gol_rh_pasien', 10)->nullable();

            $table->string('cara_pembayaran', 30)->nullable(); 
            $table->string('jns_biaya', 100)->nullable();      

            $table->string('no_reg_online', 30)->nullable();
            $table->date('tgl_registrasi_online')->nullable();

            $table->string('petugas', 100)->nullable();

            $table->string('kurir_rs', 100)->nullable();

            $table->boolean('pasien_referal')->default(false);

            $table->boolean('export_dropping')->default(false);
            $table->dateTime('tgl_export_dropping')->nullable();

            $table->string('status', 20)->default('baru'); 
            $table->text('keterangan')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('permintaan_fpup_id')
                  ->references('id')
                  ->on('permintaan_fpup')
                  ->nullOnDelete();
        });

        Schema::create('pemberian_darah_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pemberian_darah_id');
            $table->unsignedBigInteger('stok_darah_id')->nullable();
            $table->string('no_stok', 50)->nullable();
            $table->string('jns_darah', 50)->nullable();
            $table->string('gol', 5)->nullable();
            $table->string('rhesus', 10)->nullable();
            $table->date('tgl_expired')->nullable();

            $table->string('metode', 30)->nullable(); 
            $table->string('hasil', 30)->nullable();  
            $table->text('keterangan')->nullable();

            $table->integer('jumlah')->default(1);
            $table->integer('cc')->nullable();

            $table->decimal('harga_satuan', 12, 2)->default(0);
            $table->decimal('total_harga', 12, 2)->default(0);

            $table->timestamps();

            $table->foreign('pemberian_darah_id')
                  ->references('id')
                  ->on('pemberian_darah')
                  ->onDelete('cascade');

            $table->foreign('stok_darah_id')
                  ->references('id')
                  ->on('stok_darah')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemberian_darah_detail');
        Schema::dropIfExists('pemberian_darah');
    }
};