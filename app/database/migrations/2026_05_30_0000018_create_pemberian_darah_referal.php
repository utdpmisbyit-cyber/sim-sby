<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemberian_darah_referal', function (Blueprint $table) {
            $table->id();
            $table->string('no_pemberian', 20)->unique()->comment('Nomor pemberian auto-generate');
            $table->date('tanggal');
            $table->time('jam_keluar')->nullable();
            $table->string('petugas_kode', 20)->nullable();
            $table->string('petugas_nama', 100)->nullable();
            $table->string('nama_penerima', 100)->nullable();
            $table->text('alamat_penerima')->nullable();

            // Data Permintaan (FPUP)
            $table->string('no_fpup', 20)->nullable()->index();
            $table->dateTime('tgl_fpup')->nullable();
            $table->string('dokter', 100)->nullable();
            $table->string('kode_rs', 20)->nullable();
            $table->string('nama_rs', 150)->nullable();
            $table->string('pasien', 100)->nullable();
            $table->string('jenis_rs', 30)->nullable()->comment('SWASTA / PEMERINTAH / dll');
            $table->string('kelas_rawat', 30)->nullable()->comment('Kelas 1 / Kelas 2 / Kelas 3 / VIP');
            $table->string('gol_darah_pasien', 5)->nullable()->comment('A / B / AB / O');
            $table->string('rh_pasien', 10)->nullable()->comment('Positif / Negatif');
            $table->string('kategori', 5)->nullable()->comment('Kategori RS');
            $table->string('utdd_lain', 50)->nullable();
            $table->string('jns_biaya', 50)->nullable()->comment('BIASA / BPJS / dll');

            // Jenis darah yang dikirim (hasil pilih dari tabel "Pilih Jenis Darah")
            $table->string('jns_darah_kirim', 20)->nullable()->comment('PRC / WB / TC / dll');
            $table->string('gol_darah_kirim', 5)->nullable();
            $table->string('rh_kirim', 10)->nullable();
            $table->unsignedSmallInteger('jumlah_kantong')->default(0);
            $table->unsignedSmallInteger('dilayani')->default(0);
            $table->string('kurir_rs', 100)->nullable();

            // Opsi — sesuai checkbox pada tampilan: "Kadaluarsa" & "Pasien Bayi"
            $table->boolean('is_kadaluarsa')->default(false);
            $table->boolean('is_pasien_bayi')->default(false);

            // Registrasi Online
            $table->string('no_registrasi_online', 50)->nullable();
            $table->dateTime('tgl_registrasi_online')->nullable();

            $table->string('status', 20)->default('draft')->comment('draft / proses / selesai / batal');
            $table->timestamps();
            $table->softDeletes();
        });

       
        Schema::create('pemberian_darah_referal_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemberian_darah_id')
                  ->constrained('pemberian_darah_referal')
                  ->onDelete('cascade');
            $table->string('nostock', 30)->nullable()->comment('Nomor stok kantong darah');
            $table->string('jns_darah', 20)->nullable()->comment('PRC / WB / TC / FFP / dll');
            $table->string('gol', 5)->nullable()->comment('A / B / AB / O');
            $table->string('rh', 10)->nullable()->comment('Positif / Negatif');
            $table->date('tgl_expired')->nullable();
            $table->string('metode', 50)->nullable()->comment('GEL / Tube Method / dll — metode crossmatch per kantong');
            $table->string('hasil', 50)->nullable()->comment('Cocok / Tidak Cocok — hasil crossmatch per kantong');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemberian_darah_referal_detail');
        Schema::dropIfExists('pemberian_darah_referal');
    }
};