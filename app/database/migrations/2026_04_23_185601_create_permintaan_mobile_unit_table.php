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
        Schema::create('permintaan_mobile_unit', function (Blueprint $table) {
            $table->id();
            $table->string('nomor')->unique();
            $table->date('tanggal')->nullable();
            $table->text('keterangan')->nullable();
            $table->tinyInteger('flag')->default(0);
            $table->timestamps();
            $table->foreignId('bagian_petugas_id')->constrained('bagian_petugas');
            $table->foreignId('petugas_id')->constrained('petugas');
            $table->foreignId('verifikator_id')->nullable()->constrained('petugas');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_mobile_unit');
    }
};