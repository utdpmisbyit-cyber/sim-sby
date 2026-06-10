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
        Schema::create('pengembalian_kantong_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengembalian_kantong_id')->constrained('pengembalian_kantong');
            $table->foreignId('tipe_kantong_id')->nullable()->constrained('tipe_kantong');
            $table->integer('jumlah');
            $table->smallInteger('flag')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian_kantong_detail');
    }
};
