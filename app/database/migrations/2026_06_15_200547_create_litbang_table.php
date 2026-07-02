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
        Schema::create('litbang', function (Blueprint $table) {
            $table->id();
            $table->string('no_kantong')->unique(); // 1 Litbang, 1 No Kantong
            $table->foreignId('aftap_id')
                ->nullable()
                ->constrained('aftap')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignId('donor_id')
                ->nullable()
                ->constrained('donor')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->string('status')->default('pending'); // 'pending' (sent), 'selesai' (confirmed)
            $table->date('tanggal_kirim')->nullable();
            $table->date('tanggal_konfirmasi')->nullable();
            $table->string('golongan_darah')->nullable();
            $table->string('rhesus')->nullable();
            $table->foreignId('petugas_kirim_id')
                ->nullable()
                ->constrained('petugas')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignId('petugas_konfirmasi_id')
                ->nullable()
                ->constrained('petugas')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->text('keterangan')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('litbang');
    }
};
