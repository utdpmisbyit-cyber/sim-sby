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
        Schema::create('pengiriman_darah_prolis', function (Blueprint $table) {
            $table->id();
            $table->string('no_pengiriman')->unique();
            $table->date('tgl_pengiriman');
            $table->string('no_stok')->nullable();
            $table->string('no_kantong')->nullable();
            $table->string('jenis_darah')->nullable();
            $table->string('golongan_darah')->nullable();
            $table->string('rhesus')->nullable();
            $table->date('tgl_aftap')->nullable();
            $table->date('tgl_produksi')->nullable();
            $table->date('tgl_expired')->nullable();
            $table->string('nama_asal_darah')->nullable();
            $table->string('status')->nullable();
            $table->string('suhu')->nullable();
            $table->integer('gr')->nullable();
            $table->integer('ml')->nullable();
            $table->integer('jumlah')->default(0);
            $table->string('skrining')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('no_fpd')->nullable();

            // Foreign Keys
            $table->unsignedBigInteger('asal_darah_id')->nullable();
            $table->unsignedBigInteger('petugas_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign Constraints
            $table->foreign('asal_darah_id')
                  ->references('id')
                  ->on('asal_darah')
                  ->nullOnDelete();

            $table->foreign('petugas_id')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();

            $table->foreign('created_by')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengiriman_darah_prolis');
    }
};