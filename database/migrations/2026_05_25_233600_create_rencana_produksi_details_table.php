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
        Schema::create('rencana_produksi_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rencana_produksi_id')->constrained('rencana_produksi');
            $table->string('no_kantong')->nullable();
            $table->string('no_satelit')->nullable();
            $table->string('jenis_darah')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rencana_produksi_detail');
    }
};
