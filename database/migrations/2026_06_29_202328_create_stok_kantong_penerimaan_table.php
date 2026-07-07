<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::create('stok_kantong_penerimaan', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi', 30)->unique();
            $table->date('tanggal');
            $table->string('no_keluar', 20)->nullable()->index();
            $table->string('kode_permintaan', 50)->nullable()->index();
            $table->unsignedBigInteger('permintaan_kantong_id')->nullable()->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        
          Schema::create('stok_kantong_penerimaan_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penerimaan_id')->index();
            $table->string('no_kantong', 100)->index();
            $table->string('no_lot', 50)->nullable();
            $table->string('merk', 100)->nullable();
            $table->string('jenis', 100)->nullable();
            $table->string('ukuran', 50)->nullable();
 
            // tersedia | sample | serologi
            $table->string('status_kirim', 20)->default('tersedia');
            $table->text('info_kirim')->nullable();
            $table->unsignedBigInteger('penyisihan_detail_id')->nullable()->index();
            $table->timestamps();
 
            $table->foreign('penerimaan_id')
                  ->references('id')->on('stok_kantong_penerimaan')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_kantong_penerimaan_detail');
        Schema::dropIfExists('stok_kantong_penerimaan');
    }
};
