<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apheresis_pengambilan_darah', function (Blueprint $table) {
            $table->id();

            // Header
            $table->string('no_transaksi', 30)->unique();
            $table->timestamp('server_date')->nullable();
            // Tanpa foreign key constraint (lihat catatan modul sampling pra donor) -
            // sesuaikan/tambahkan FK manual kalau tipe kolom id di tabel users sudah dipastikan cocok.
            $table->unsignedBigInteger('petugas_id')->nullable()->index();

            $table->string('no_sampling', 30)->nullable()->index();
            $table->string('no_donor', 30)->nullable()->index();
            $table->string('nama_donor')->nullable();
            $table->date('tgl_lahir')->nullable();

            // Alat & bahan
            $table->string('type_mesin')->nullable();
            $table->string('no_mesin')->nullable();
            $table->string('operator')->nullable();
            $table->string('kode_disposable_kit')->nullable();
            $table->string('type_ac_ratio')->nullable();
            $table->string('cairan_saline')->nullable();

            $table->string('no_lot_1')->nullable();
            $table->date('kadaluarsa_lot_1')->nullable();
            $table->string('no_lot_2')->nullable();
            $table->date('kadaluarsa_lot_2')->nullable();
            $table->string('no_lot_3')->nullable();
            $table->date('kadaluarsa_lot_3')->nullable();

            $table->enum('golongan_darah', ['A', 'B', 'AB', 'O'])->nullable();
            $table->enum('rhesus', ['positif', 'negatif'])->nullable();
            $table->enum('jenis_kelamin', ['pria', 'wanita'])->nullable();

            $table->enum('riwayat_donor_sebelumnya', ['pernah', 'tidak_pernah'])->nullable();
            $table->unsignedInteger('riwayat_donor_sebelumnya_kali')->nullable();
            $table->enum('riwayat_donor_apheresis', ['pernah', 'tidak_pernah'])->nullable();
            $table->unsignedInteger('riwayat_donor_apheresis_kali')->nullable();

            // Haemocalculator (sebelum prosedur)
            $table->decimal('tinggi_badan', 6, 2)->nullable();
            $table->decimal('berat_badan', 6, 2)->nullable();
            $table->decimal('hct', 6, 2)->nullable();
            $table->decimal('platelet_precount', 10, 2)->nullable();
            $table->decimal('target_vol_plasma', 10, 2)->nullable();
            $table->decimal('target_platelet_yield', 10, 2)->nullable();
            $table->unsignedInteger('target_cycle')->nullable();
            $table->string('target_waktu')->nullable();
            $table->decimal('estimasi_vol_plt', 10, 2)->nullable();

            // Prosedur
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->string('durasi')->nullable();
            $table->decimal('vol_wb_terproses', 10, 2)->nullable();
            $table->decimal('vol_ac_terpakai', 10, 2)->nullable();
            $table->decimal('vol_saline_terpakai', 10, 2)->nullable();
            $table->decimal('draw_rate', 10, 2)->nullable();
            $table->decimal('return_rate', 10, 2)->nullable();
            $table->decimal('plt_hct_postcount', 10, 2)->nullable();

            // Haemocalculator (setelah prosedur) - Platelet
            $table->decimal('platelet_total_vol_aktual', 10, 2)->nullable();
            $table->decimal('platelet_vol_plt', 10, 2)->nullable();
            $table->decimal('platelet_vol_plasma_dlm_plt', 10, 2)->nullable();
            $table->decimal('platelet_ac_dlm_plt', 10, 2)->nullable();
            $table->decimal('platelet_yield_plt', 10, 2)->nullable();

            // Haemocalculator (setelah prosedur) - Plasma
            $table->decimal('plasma_total_vol_aktual', 10, 2)->nullable();
            $table->decimal('plasma_vol_plasma', 10, 2)->nullable();
            $table->decimal('plasma_ac_dlm_plasma', 10, 2)->nullable();

            $table->text('catatan')->nullable();
            $table->string('operator_akhir')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('apheresis_pengambilan_darah_siklus', function (Blueprint $table) {
            $table->id();
 
            // Tanpa foreign key constraint bawaan - lihat catatan di migration header.
            $table->unsignedBigInteger('pengambilan_darah_id')->index();
 
            $table->unsignedInteger('siklus_ke');
            $table->time('jam')->nullable();
            $table->decimal('draw_return_ml', 10, 2)->nullable();
            $table->decimal('draw_return_menit', 10, 2)->nullable();
            $table->decimal('plasma_vol', 10, 2)->nullable();
            $table->decimal('platelet_yield', 10, 2)->nullable();
            $table->decimal('plasma_vol_2', 10, 2)->nullable();
            $table->decimal('nacl_sitrat', 10, 2)->nullable();
            $table->string('keterangan')->nullable();
 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apheresis_pengambilan_darah_siklus');
        Schema::dropIfExists('apheresis_pengambilan_darah');
    }
};