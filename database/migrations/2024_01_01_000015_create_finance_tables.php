<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coa', function (Blueprint $table) {
            $table->id();
            $table->string('kd_coa')->unique();
            $table->string('kategori_1');
            $table->string('kategori_2');
            $table->string('nama_akun')->unique();
            $table->string('possaldo');
            $table->string('poslaporan');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('trial_balance', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->double('sa_debet')->default(0);
            $table->double('sa_kredit')->default(0);
            $table->double('debet')->default(0);
            $table->double('kredit')->default(0);
            $table->double('laba_debet')->default(0);
            $table->double('laba_kredit')->default(0);
            $table->double('neraca_debet')->default(0);
            $table->double('neraca_kredit')->default(0);
            $table->string('kategori1')->nullable();
            $table->string('kategori2')->nullable();
            $table->string('pos_saldo')->nullable();
            $table->string('pos_laporan')->nullable();
            $table->foreignId('coa_id')->unique()->constrained('coa')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('nama_akun')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Pivot table: Coa <-> ProgramKerja (many-to-many)
        Schema::create('coa_program_kerja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coa_id')->constrained('coa')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('program_kerja_id')->constrained('program_kerja')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['coa_id', 'program_kerja_id']);
        });

        Schema::create('penyesuaian', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('program_kerja');
            $table->text('dokumen')->nullable();
            $table->string('ref_bayar');
            $table->string('transaksi_coa');
            $table->string('nominal_debit');
            $table->string('nominal_kredit');
            $table->text('keterangan');
            $table->string('jenis_saldo');
            $table->timestamp('tgl')->nullable();
            $table->foreignId('program_kerja_id')->nullable()->constrained('program_kerja')->cascadeOnUpdate()->nullOnDelete();
            $table->string('nama_akun')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('general_leadge', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('no_dokumen');
            $table->string('program_kerja');
            $table->string('referensi');
            $table->foreignId('coa_id')->nullable()->constrained('coa')->cascadeOnUpdate()->nullOnDelete();
            $table->string('nominal_debit');
            $table->string('nominal_kredit');
            $table->text('keterangan');
            $table->string('saldo_awal');
            $table->string('dibayarkan_ke');
            $table->string('rekening_kas');
            $table->string('kode_transaksi');
            $table->string('nominal_rp');
            $table->string('lawan_transaksi');
            $table->string('terima_dari');
            $table->string('bs');
            $table->string('pl');
            $table->string('inventory');
            $table->string('hutang');
            $table->string('piutang');
            $table->timestamp('tgl');
            $table->foreignId('program_kerja_id')->nullable()->constrained('program_kerja')->cascadeOnUpdate()->nullOnDelete();
            $table->string('nama_akun')->nullable();
            $table->foreignId('penyesuaian_id')->nullable()->constrained('penyesuaian')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('trial_balance_id')->nullable()->constrained('trial_balance')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('kas_keluar', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('program_kerja');
            $table->string('dokumen');
            $table->string('dibayar_ke');
            $table->string('ref_an');
            $table->string('rekning_kas');
            $table->string('transaksi');
            $table->string('nominal');
            $table->text('keterangan');
            $table->timestamp('tgl')->nullable();
            $table->foreignId('program_kerja_id')->nullable()->constrained('program_kerja')->cascadeOnUpdate()->nullOnDelete();
            $table->string('nama_akun')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('kas_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('program_kerja');
            $table->string('dokumen');
            $table->string('ref_an');
            $table->string('rekning_kas');
            $table->string('transaksi');
            $table->string('nominal');
            $table->text('keterangan');
            $table->timestamp('tgl')->nullable();
            $table->foreignId('program_kerja_id')->nullable()->constrained('program_kerja')->cascadeOnUpdate()->nullOnDelete();
            $table->string('nama_akun')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('rekanan', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama_rekanan')->unique();
            $table->string('kategori');
            $table->string('tlp');
            $table->string('email');
            $table->text('alamat');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekanan');
        Schema::dropIfExists('kas_masuk');
        Schema::dropIfExists('kas_keluar');
        Schema::dropIfExists('general_leadge');
        Schema::dropIfExists('penyesuaian');
        Schema::dropIfExists('coa_program_kerja');
        Schema::dropIfExists('trial_balance');
        Schema::dropIfExists('coa');
    }
};
