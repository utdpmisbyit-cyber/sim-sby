<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fpup', function (Blueprint $table) {
            $table->id();

            // Data identitas pasien
            $table->string('nama_pasien', 100);
            $table->string('no_ktp', 20)->nullable()->unique();
            $table->date('tgl_lahir')->nullable();
            $table->integer('umur')->nullable();
            $table->string('jenis_kelamin', 10)->nullable();
            $table->string('kebangsaan', 50)->nullable();
            $table->string('no_telp', 20)->nullable();
            $table->text('alamat')->nullable();

            $table->json('keluarga')->nullable();

            $table->string('nama_dokter', 100)->nullable();
            $table->string('nama_instansi', 100)->nullable();

            // Foto KTP + hasil OCR (verifikasi foto)
            $table->string('foto_ktp_path')->nullable();
            $table->json('ocr_raw_result')->nullable();      // hasil mentah OCR (debug/audit)
            $table->boolean('ocr_terverifikasi')->default(false); // ditandai user setelah cek manual
            $table->timestamp('ocr_verified_at')->nullable();
            $table->string('ocr_verified_by')->nullable();

            $table->text('keterangan')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        // FK dari permintaan_fpup ke master pasien fpup
        Schema::table('permintaan_fpup', function (Blueprint $table) {
            $table->unsignedBigInteger('fpup_id')->nullable()->after('id');
            $table->foreign('fpup_id')
                  ->references('id')
                  ->on('fpup')
                  ->onDelete('set null');
        });

        // FK dari permintaan_fpup_referal ke master pasien fpup
        Schema::table('permintaan_fpup_referal', function (Blueprint $table) {
            $table->unsignedBigInteger('fpup_id')->nullable()->after('id');
            $table->foreign('fpup_id')
                  ->references('id')
                  ->on('fpup')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_fpup_referal', function (Blueprint $table) {
            $table->dropForeign(['fpup_id']);
            $table->dropColumn('fpup_id');
        });

        Schema::table('permintaan_fpup', function (Blueprint $table) {
            $table->dropForeign(['fpup_id']);
            $table->dropColumn('fpup_id');
        });

        Schema::dropIfExists('fpup');
    }
};