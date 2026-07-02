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
        Schema::create('permintaan_mobile_unit_detail', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('permintaan_mu_id');
            $table->unsignedBigInteger('tipe_kantong_id')->nullable();

            $table->integer('jumlah')->default(0);
            $table->integer('jumlah_dilayani')->default(0);

            $table->string('kode')->nullable();
            $table->string('merk')->nullable();
            $table->string('jenis')->nullable();
            $table->string('ukuran')->nullable();

            $table->string('status')->nullable();

            // 0=draft,1=proses,2=selesai dll
            $table->tinyInteger('flag')->default(0);

            $table->timestamps();

            // Foreign Keys
            $table->foreign('permintaan_mu_id')
                  ->references('id')
                  ->on('permintaan_mobile_unit')
                  ->cascadeOnDelete();

            $table->foreign('tipe_kantong_id')
                  ->references('id')
                  ->on('tipe_kantong')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_mobile_unit_detail');
    }
};