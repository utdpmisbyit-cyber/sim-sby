<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('permintaan_darah_external', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_permintaan', 50)->unique();
            $table->date('tanggal');
            $table->string('petugas', 20);
            $table->string('petugas_kode', 50);
            $table->string('nama_peminta', 100);
            $table->string('institusi_lain', 150);
            $table->enum('jenis_biaya', ['Dropping',  'Konfalesen','BPJS', 'ASURASI',])->default('Dropping');
            $table->enum('dropping', ['AMBIL_SENDIRI', 'DIANTAR', 'KURIR'])->nullable();
            $table->enum('status', ['SUDAH_DIPENUHI', 'BELUM_DIPENUHI', 'SEBAGIAN'])->default('BELUM_DIPENUHI');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('permintaan_darah_external_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permintaan_id')->constrained('permintaan_darah_external')->onDelete('cascade');
            $table->string('jenis_darah', 50)->nullable();
            $table->enum('donor_pengganti', ['Ya','Tidak']);
            $table->string('no_fpup', 50)->nullable();
            $table->string('fpup_id', 50)->nullable();
            $table->enum('gol_darah', ['A', 'B', 'O', 'AB']);
            $table->enum('rhesus', ['Positif', 'Negatif']);
            $table->integer('jumlah')->nullable();
            $table->integer('jumlah_dipenuhi')->default(0);
            $table->date('tanggal_perlu')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('permintaan_darah_external_detail');
        Schema::dropIfExists('permintaan_darah_external');
    }
};