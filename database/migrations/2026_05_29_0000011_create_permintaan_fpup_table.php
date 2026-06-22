<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
         Schema::create('permintaan_fpup', function (Blueprint $table) {
            $table->id();
         
            // Nomor FPUP & Registrasi
            $table->string('no_fpup', 30)->unique();
            $table->string('no_reg', 20)->nullable();
            $table->string('no_reg_online', 20)->nullable();
            $table->date('tgl_minta');
            $table->time('jam_minta')->nullable();
            $table->date('tgl_registrasi_online')->nullable();

            // Data Rumah Sakit
            $table->string('kode_rs', 20)->nullable();
            $table->string('nama_rs', 100)->nullable();
            $table->string('jenis_rs', 50)->nullable();       // SWASTA, PEMERINTAH, dll
            $table->string('kategori_rs', 50)->nullable();    // KEL, UTD Lain, dll
            $table->string('bagian', 50)->nullable();         // ICU, IGD, dll
            $table->string('kelas_rawat', 50)->nullable();    // Kelas 1, 2, 3

            // Data Pasien
            $table->string('nama_pasien', 100);
            $table->string('no_ktp', 20)->nullable()->unique();
            $table->date('tgl_lahir')->nullable();
            $table->integer('umur')->nullable();
            $table->string('kebangsaan', 50)->nullable();
            $table->string('jenis_kelamin', 10)->nullable();  // Pria / Wanita
            $table->text('alamat')->nullable();
            $table->string('no_telp', 20)->nullable();
            $table->string('nama_suami_istri', 100)->nullable();

            // Khusus Wanita
            $table->integer('jumlah_kehamilan')->nullable();
            $table->string('abortus', 10)->nullable();
            $table->boolean('hdn')->default(false);           // Penyakit hemolitik bayi

            // Data Permintaan
            $table->string('jns_permintaan', 30)->nullable(); // CITO, Biasa, Elektif
            $table->string('diagnosa_klinis', 100)->nullable();
            $table->string('hb', 10)->nullable();
            $table->text('alasan_transfusi')->nullable();
            $table->boolean('transfusi_sebelumnya')->default(false);
            $table->date('transfusi_kapan')->nullable();
            $table->boolean('reaksi_transfusi')->default(false);
            $table->text('reaksi_gejala')->nullable();
            $table->boolean('pernah_serologi')->default(false);
            $table->string('serologi_dimana', 100)->nullable();
            $table->string('serologi_hasil', 50)->nullable();
            $table->date('serologi_kapan')->nullable();

            // Data Darah OS (dari UTDD)
            $table->string('nama_darah_os', 100)->nullable();
            $table->string('gol_rh_os', 10)->nullable();       // O+, A-, dll
            $table->date('tgl_terima')->nullable();
            $table->time('jam_terima')->nullable();
            $table->string('pemeriksa', 100)->nullable();
            $table->boolean('pasien_referal')->default(false);
            $table->text('alasan_referal')->nullable();
            $table->boolean('cetak_barcode')->default(false);

            // Cara Pembayaran & Donor
            $table->string('cara_pembayaran', 30)->nullable(); // TAGIHAN, TUNAI, BPJS
            $table->string('jns_biaya', 100)->nullable();      // NATBPPD, dll
            $table->string('jns_donor', 20)->nullable();       // Sukarela / Pengganti
            $table->integer('jml_donor')->default(0);
            $table->string('nama_dokter', 100)->nullable();
            $table->string('nama_os', 100)->nullable();        // Nama pasien alias

            $table->string('status', 20)->default('baru');    // baru, proses, selesai
            $table->softDeletes();
            $table->timestamps();
        });

        // ③ Tabel detail darah
        Schema::create('permintaan_fpup_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('permintaan_fpup_id');
            $table->string('jns_darah', 50);
            $table->string('gol_darah', 5)->nullable();
            $table->string('rhesus', 10)->nullable();
            $table->integer('jumlah')->default(1);
            $table->integer('cc')->nullable();
            $table->date('tgl_perlu')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('permintaan_fpup_id')
                  ->references('id')
                  ->on('permintaan_fpup')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_fpup_detail');
        Schema::dropIfExists('permintaan_fpup');
    }
};