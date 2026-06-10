<?php

namespace App\Services;

use App\Models\KasMasuk;
use App\Models\GeneralLeadge;
use App\Models\TrialBalance;
use App\Models\ProgramKerja;
use App\Models\Coa;

class KasMasukService extends IoService
{
    public function __construct()
    {
        $this->model = new KasMasuk();
        $this->sort_by = ['tgl' => 'desc'];
        $this->filters = ['kode', 'program_kerja_id'];
    }

    /*
    |-------------------------------------------------
    | FUNGSI KHUSUS UPDATE / CREATE TRIAL BALANCE
    |-------------------------------------------------
    */
 private function updateTrialBalance($coa, $kode, $debet, $kredit)
{
    // cek apakah trial balance sudah ada
    $tb = TrialBalance::where('kode', $kode)->first();

    if (!$tb) {
        // BUAT BARU
        $tb = TrialBalance::create([
            'kode'          => $kode,
            'coa_id'        => $coa->id,
            'nama_akun'     => $coa->nama_akun,

            'sa_debet'      => 0,
            'sa_kredit'     => 0,

            'debet'         => $debet,
            'kredit'        => $kredit,

            'laba_debet'    => 0,
            'laba_kredit'   => 0,
            'neraca_debet'  => 0,
            'neraca_kredit' => 0,

            'kategori1'     => $coa->kategori_1,
            'kategori2'     => $coa->kategori_2,
            'pos_saldo'     => $coa->possaldo,
            'pos_laporan'   => $coa->poslaporan,
        ]);
    } else {
        // UPDATE SALDO
        $tb->debet  += $debet;
        $tb->kredit += $kredit;
        $tb->save();
    }

    return $tb;
}

    /*
    |-------------------------------------------------
    | STORE
    |-------------------------------------------------
    */
    public function store($data)
    {
        // AMBIL PROGRAM KERJA
        $program = ProgramKerja::find($data['program_kerja_id']);
        $data['program_kerja'] = $program->nama_program ?? null;

        // SIMPAN KAS MASUK
        $kas = KasMasuk::create($data);

        // GET COA
        $coa = Coa::where('nama_akun', $data['nama_akun'])->first();
        if (!$coa) {
            throw new \Exception("COA tidak ditemukan!");
        }
        
        // update Trial Balance
        $tb = $this->updateTrialBalance(
         $coa,
         $data['kode'],
         $data['nominal'],
          0);

        // General Leadge
        GeneralLeadge::create([
            'kode'             => $data['kode'],
            'no_dokumen'       => $data['dokumen'] ?? null,
            'program_kerja'    => $data['program_kerja'],
            'referensi'        => $data['ref_an'] ?? null,
            'coa_id'           => $coa->id,
            'nominal_debit'    => $data['nominal'],
            'nominal_kredit'   => 0,
            'keterangan'       => $data['keterangan'] ?? null,
            'saldo_awal'       => $tb->debet ?? 0,

            'dibayarkan_ke'    => $data['nama_akun'],
            'rekening_kas'     => $data['rekning_kas'] ?? null,
            'kode_transaksi'   => $data['transaksi'] ?? null,
            'nominal_rp'       => $data['nominal'],
            'terima_dari'      => $data['nama_akun'],
            'lawan_transaksi'  => $data['nama_akun'],

            'bs'               => $data['nominal'],
            'pl'               => $data['nominal'],

            'inventory'        => 0,
            'hutang'           => 0,
            'piutang'          => 0,

            'tgl'              => $data['tgl'],
            'program_kerja_id' => $data['program_kerja_id'],
            'nama_akun'        => $data['nama_akun'],

            'penyesuaian_id'   => $data['penyesuaian_id'] ?? null,

            'trial_balance_id' => $tb->id,
        ]);

        return $kas;
    }

    /*
    |-------------------------------------------------
    | UPDATE
    |-------------------------------------------------
    */
    public function update($data, $kas)
{
    $selisih = $data['nominal'] - $kas->nominal;

    $program = ProgramKerja::find($data['program_kerja_id']);
    $data['program_kerja'] = $program->nama_program ?? null;

    // UPDATE KAS
    $kas->update($data);

    // GET GL
    $gl = GeneralLeadge::where('kode', $kas->kode)->first();
    $coa = Coa::where('nama_akun', $data['nama_akun'])->firstOrFail();

    // UPDATE TRIAL BALANCE pakai selisih
    $tb = $this->updateTrialBalance(
            $coa,
            $kas->kode,       
            $selisih,    
            0);

    // UPDATE GENERAL LEDGER
    if ($gl) {
        $gl->update([
            'no_dokumen'       => $data['dokumen'],
            'program_kerja'    => $data['program_kerja'],
            'referensi'        => $data['ref_an'],
            'coa_id'           => $coa->id,
            'nominal_debit'    => $data['nominal'],
            'keterangan'       => $data['keterangan'],
            'saldo_awal'       => $tb->debet,
            'rekening_kas'     => $data['rekning_kas'],
            'kode_transaksi'   => $data['transaksi'],
            'nominal_rp'       => $data['nominal'],
            'tgl'              => $data['tgl'],
            'program_kerja_id' => $data['program_kerja_id'],
        ]);
    }

    return $kas;
}

    /*
    |-------------------------------------------------
    | DELETE
    |-------------------------------------------------
    */
    public function delete($kas)
    {
        // ambil GL
        $gl = GeneralLeadge::where('kode', $kas->kode)->first();

        if ($gl) {

            // Ambil COA
            $coa = Coa::where('kd_coa', $gl->coa_id)->first();

            // kurangi trial balance (pakai minus)
            $tb = TrialBalance::where('kode', $kas->kode)->first();

            if ($tb) {
                $tb->debet  -= $gl->nominal_debit;
                $tb->kredit -= $gl->nominal_kredit;

                // jika sudah 0 semua → hapus baris TB
                if ($tb->debet == 0 && $tb->kredit == 0) {
                    $tb->delete();
                } else {
                    $tb->save();
                }
            }

            // hapus GL
            $gl->delete();
        }

        // terakhir hapus kas masuk
        $kas->delete();
    }
}