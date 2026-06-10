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
        Schema::create('penyimpanan_kantong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bagian_petugas_id')->constrained('bagian_petugas');
            $table->foreignId('tipe_kantong_id')->constrained('tipe_kantong');
            $table->integer('jumlah')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyimpanan_kantong');
    }
};
