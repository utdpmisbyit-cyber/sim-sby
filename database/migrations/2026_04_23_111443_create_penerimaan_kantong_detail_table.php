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
        Schema::create('penerimaan_kantong_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penerimaan_id')
                  ->constrained('penerimaan_kantong')
                  ->onDelete('cascade');

            $table->string('no_kantong');
            $table->string('merk')->nullable();
            $table->string('jenis')->nullable();
            $table->string('ukuran')->nullable();
            $table->string('no_lot')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penerimaan_kantong_detail');
    }
};