<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengembalian_kantong', function (Blueprint $table) {
            $table->id();
            $table->string('no_kembali', 20)->index()->comment('Format: KBYYMMxxxxxx');
            $table->date('tgl_kembali');
            $table->string('no_kantong', 100)->index();

            // FK ke stok_kantong_penerimaan_detail.id (data stok fisik per-kantong
            // hasil alur Penerimaan), BUKAN ke stok_kantong_masuk.
            $table->unsignedBigInteger('stok_kantong_id')->index()
                  ->comment('FK ke stok_kantong_penerimaan_detail.id');

            $table->foreignId('asal_darah_id')
                ->nullable()
                ->constrained('asal_darah')
                ->nullOnDelete();

            $table->string('merk', 100)->nullable();
            $table->string('jenis', 100)->nullable();
            $table->string('tipe', 100)->nullable();
            $table->string('ukuran', 50)->nullable();
            $table->enum('kondisi', ['baik', 'rusak'])->default('baik');
            $table->string('keterangan', 255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['no_kembali', 'tgl_kembali']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengembalian_kantong');
    }
};