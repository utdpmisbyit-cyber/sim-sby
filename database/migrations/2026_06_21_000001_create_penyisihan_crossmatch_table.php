<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyisihan_crossmatch', function (Blueprint $table) {
            $table->id();

            $table->string('no_penyisihan', 30)->unique();  
            $table->date('tanggal_penyisihan');
            $table->string('petugas', 100)->nullable();

            $table->unsignedInteger('jumlah')->default(0);   
            $table->enum('status', ['draft', 'selesai'])->default('selesai');
            $table->text('keterangan')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index('tanggal_penyisihan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyisihan_crossmatch');
    }
};