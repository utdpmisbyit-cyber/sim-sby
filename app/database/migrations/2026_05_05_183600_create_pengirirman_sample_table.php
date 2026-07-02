<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengiriman_sample', function (Blueprint $table) {
            $table->id();
            $table->string('no_fpd')->unique(); 
            $table->date('tanggal_fpd')->nullable();
            $table->integer('total')->default(0); 
            $table->string('stok')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('type_kantong')->nullable();
            $table->string('suhu')->nullable();
            $table->boolean('is_nat')->default(false);
            $table->string('petugas_pemeriksa')->nullable();
            $table->string('id_logger')->nullable();
            $table->string('id_coolbox')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        
        Schema::dropIfExists('pengiriman_sample');
    }
};