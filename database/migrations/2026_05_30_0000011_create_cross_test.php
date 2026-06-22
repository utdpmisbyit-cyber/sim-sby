<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cross_tests', function (Blueprint $table) {
            $table->id();

            // Referensi ke permintaan FPUP
            $table->unsignedBigInteger('permintaan_fpup_id');
            $table->string('no_fpup', 30);                       

            // Identitas Pasien (mirror dari FPUP)
            $table->string('nama_pasien', 100)->nullable();
            $table->string('gol', 10)->nullable(); 
            $table->string('rhesus', 10)->nullable();    
            // Identitas Kantong Darah
            $table->string('no_stock', 30)->nullable();
            $table->string('jns_darah', 50)->nullable();    
            $table->string('gol_rh_kantong', 10)->nullable();
            $table->date('tgl_ambil')->nullable();
            $table->date('tgl_produksi')->nullable();
            $table->date('tgl_kadaluarsa')->nullable();
            $table->date('tgl_online')->nullable();


            $table->string('referal')->nullable();
            $table->string('no_referal')->nullable();
            $table->string('kurir_online')->nullable();
            $table->text('catatan_hasil')->nullable();

            // Status & Petugas
            $table->string('pemeriksa', 100)->nullable();
            $table->datetime('tgl_periksa')->nullable();
            $table->enum('status', ['pending', 'proses', 'compatible', 'incompatible', 'selesai'])
                  ->default('pending');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('permintaan_fpup_id')
                  ->references('id')
                  ->on('permintaan_fpup')
                  ->onDelete('cascade');

            $table->index('no_fpup');
            $table->index('no_stock');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cross_tests');
    }
};