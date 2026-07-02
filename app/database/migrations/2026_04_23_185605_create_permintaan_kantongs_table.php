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
        Schema::create('permintaan_kantong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bagian_petugas_id')->constrained('bagian_petugas');
            $table->foreignId('petugas_id')->constrained('petugas');
            $table->foreignId('verifikator_id')->nullable()->constrained('petugas');
            $table->text('keterangan')->nullable();
            $table->string('nomor');
            $table->date('tanggal');
            $table->smallInteger('flag')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_kantong');
    }
};
