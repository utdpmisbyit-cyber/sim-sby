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
        Schema::create('permintaan_kantong_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permintaan_kantong_id')->constrained('permintaan_kantong');
            $table->foreignId('tipe_kantong_id')->nullable()->constrained('tipe_kantong');
            $table->integer('jumlah');
            $table->integer('jumlah_dilayani')->default(0);
            $table->string('kode')->nullable();
            $table->string('merk')->nullable();
            $table->string('jenis')->nullable();
            $table->string('ukuran')->nullable();
            $table->string('status')->nullable();
            $table->smallInteger('flag')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_kantong_detail');
    }
};
