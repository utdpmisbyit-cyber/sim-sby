<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hak_akses', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hak_akses_petugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petugas_id')->constrained('petugas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('hak_akses_id')->constrained('hak_akses')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['petugas_id', 'hak_akses_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hak_akses_petugas');
        Schema::dropIfExists('hak_akses');
    }
};
