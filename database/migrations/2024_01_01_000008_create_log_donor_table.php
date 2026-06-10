<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_donor', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('cabang_id')->constrained('cabang')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('donor_id')->constrained('donor')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('petugas_registrasi_id')->constrained('petugas')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('step')->nullable();
            $table->string('status')->nullable();
            $table->string('nomor_ruangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_donor');
    }
};
