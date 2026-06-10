<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengeluaran_kantong_mobile_unit', function (Blueprint $table) {

            $table->id();

            $table->string('no_keluar')->unique();
            $table->date('tgl_keluar');

            $table->string('no_kantong')->nullable();
            $table->string('no_lot')->nullable();
            $table->string('merk')->nullable();
            $table->string('jenis')->nullable();
            $table->string('tipe')->nullable();
            $table->string('ukuran')->nullable();

            $table->integer('jumlah')->default(0);

            $table->string('tujuan')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('no_permintaan')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Foreign Keys Manual
            |--------------------------------------------------------------------------
            */

            // bagian_petugas
            $table->unsignedBigInteger('bagian_petugas_id')->nullable();
            $table->foreign('bagian_petugas_id', 'fk_pengeluaran_bagian')
                ->references('id')
                ->on('bagian_petugas')
                ->nullOnDelete();

            // penerimaan_kantong
            $table->unsignedBigInteger('penerimaan_kantong_id')->nullable();
            $table->foreign('penerimaan_kantong_id', 'fk_pengeluaran_penerimaan')
                ->references('id')
                ->on('penerimaan_kantong')
                ->nullOnDelete();

            // permintaan_mobile_unit
            $table->unsignedBigInteger('permintaan_mobile_unit_id')->nullable();
            $table->foreign('permintaan_mobile_unit_id', 'fk_pengeluaran_permintaan')
                ->references('id')
                ->on('permintaan_mobile_unit')
                ->nullOnDelete();

            // asal_darah
            $table->unsignedBigInteger('asal_darah_id')->nullable();
            $table->foreign('asal_darah_id', 'fk_pengeluaran_asal')
                ->references('id')
                ->on('asal_darah')
                ->nullOnDelete();

            // users
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by', 'fk_pengeluaran_user')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengeluaran_kantong_mobile_unit');
    }
};