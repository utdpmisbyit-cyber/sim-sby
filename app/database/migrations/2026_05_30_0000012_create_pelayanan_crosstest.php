<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelayanan_crosstest', function (Blueprint $table) {
            $table->id();

            // Referensi ke cross_tests
            $table->unsignedBigInteger('cross_test_id');
            $table->unsignedBigInteger('permintaan_fpup_id');
            $table->string('no_fpup', 30);

            // Identitas Kantong Darah
            $table->string('no_stock', 30)->nullable();
            $table->string('jns_darah', 50)->nullable();
            $table->string('gol', 10)->nullable();
            $table->string('rhesus', 10)->nullable();

            // Hasil Pemeriksaan
            $table->enum('metode', ['GEL', 'TUBE', 'COLUMN'])->default('GEL');
            $table->enum('hasil', ['Cocok', 'Tidak Cocok', 'Doubtful'])->nullable();
            $table->boolean('nat')->default(false);    
            $table->enum('skrining', ['NEG', 'POS', '-'])->default('-');
            $table->text('keterangan')->nullable();
            $table->text('catatan')->nullable();

            // Petugas & Waktu
            $table->string('pemeriksa', 100)->nullable();
            $table->datetime('tgl_periksa')->nullable();

            $table->enum('status', ['pending', 'proses', 'selesai', 'batal'])->default('pending');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('cross_test_id')
                  ->references('id')
                  ->on('cross_tests')
                  ->onDelete('cascade');

            $table->foreign('permintaan_fpup_id')
                  ->references('id')
                  ->on('permintaan_fpup')
                  ->onDelete('cascade');

            $table->index('no_fpup');
            $table->index('no_stock');
            $table->index('cross_test_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelayanan_crosstest');
    }
};