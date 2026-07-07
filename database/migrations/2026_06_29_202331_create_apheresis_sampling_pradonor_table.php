<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apheresis_sampling_pra_donors', function (Blueprint $table) {
            $table->id();

            // Header
            $table->string('no_transaksi', 30)->unique();
            $table->timestamp('server_date')->nullable();
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();

            $table->unsignedBigInteger('pendaftaran_id')->nullable()->index();
            $table->string('no_donor', 30)->nullable()->index();
            $table->string('nama_donor')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->enum('rhesus', ['positif', 'negatif'])->nullable();
            $table->enum('jenis_kelamin', ['pria', 'wanita'])->nullable();
            $table->enum('golongan_darah', ['A', 'B', 'AB', 'O'])->nullable();

            // Hematologi - White Blood Cell panel
            $table->decimal('wbc', 8, 2)->nullable();
            $table->decimal('neut', 8, 2)->nullable();
            $table->decimal('lymph', 8, 2)->nullable();
            $table->decimal('mono', 8, 2)->nullable();
            $table->decimal('eo', 8, 2)->nullable();
            $table->decimal('baso', 8, 2)->nullable();
            $table->decimal('ig', 8, 2)->nullable();

            // Red Blood Cell panel
            $table->decimal('rbc', 8, 2)->nullable();
            $table->decimal('hgb', 8, 2)->nullable();
            $table->decimal('hct', 8, 2)->nullable();
            $table->decimal('mcv', 8, 2)->nullable();
            $table->decimal('mch', 8, 2)->nullable();
            $table->decimal('mchc', 8, 2)->nullable();
            $table->decimal('rdw_sd', 8, 2)->nullable();
            $table->decimal('rdw_cv', 8, 2)->nullable();

            // Platelet panel
            $table->decimal('plt', 8, 2)->nullable();
            $table->decimal('pdw', 8, 2)->nullable();
            $table->decimal('mpv', 8, 2)->nullable();
            $table->decimal('p_lcr', 8, 2)->nullable();
            $table->decimal('pct', 8, 2)->nullable();

            // Hasil
            $table->enum('status_lulus', ['lulus', 'tidak_lulus'])->nullable();
            $table->json('alasan_tidak_lulus')->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apheresis_sampling_pra_donors');
    }
};