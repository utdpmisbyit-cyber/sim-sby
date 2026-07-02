<?php 

namespace App\Services;

use App\Models\KasKeluar;
use App\Models\GeneralLeadge;
use App\Models\TrialBalance;
use App\Models\ProgramKerja;
use App\Models\Coa;

class KasKeluarService extends IoService
{
    public function __construct()
    {
        $this->model = new KasKeluar();
        $this->sort_by = ['tgl' => 'desc'];
        $this->filters = ['kode', 'program_kerja_id'];
    }

    private function updateTrialBalance($coa, $kode, $debet, $kredit)
    {
        $tb = TrialBalance::where('kode', $kode)->first();

        if (!$tb) {
            return TrialBalance::create([
                'kode'          => $kode,

                // FIX → gunakan ID integer (bukan kd_coa)
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
        }

        $tb->debet  += $debet;
        $tb->kredit += $kredit;

        if ($tb->debet == 0 && $tb->kredit == 0) {
            $tb->delete();
            return null;
        }

        $tb->save();
        return $tb;
    }

    public function store($data)
    {
        $program = ProgramKerja::find($data['program_kerja_id']);
        $data['program_kerja'] = $program->nama_program ?? null;

        $data['transaksi'] = $data['nama_akun'];

        $kas = KasKeluar::create($data);

        $coa = Coa::where('nama_akun', $data['nama_akun'])->firstOrFail();

        $tb = $this->updateTrialBalance($coa, $data['kode'], 0, $data['nominal']);

        GeneralLeadge::create([
            'kode'             => $data['kode'],
            'no_dokumen'       => $data['dokumen'] ?? null,
            'program_kerja'    => $data['program_kerja'],
            'referensi'        => $data['ref_an'] ?? null,

            // FIX → gunakan id integer
            'coa_id'           => $coa->id,

            'nominal_debit'    => 0,
            'nominal_kredit'   => $data['nominal'],

            'keterangan'       => $data['keterangan'],
            'saldo_awal'       => $tb->kredit ?? 0,

            'dibayarkan_ke'    => $data['dibayar_ke'],
            'rekening_kas'     => $data['rekning_kas'],
            'kode_transaksi'   => $data['transaksi'],
            'nominal_rp'       => $data['nominal'],

            'terima_dari'      => $data['dibayar_ke'] ?? '',
            'lawan_transaksi'  => $data['nama_akun'] ?? '',

            'bs'               => $data['nominal'] ?? 0,
            'pl'               => 0,

            'inventory'        => 0,
            'hutang'           => 0,
            'piutang'          => 0,

            'tgl'              => $data['tgl'],
            'program_kerja_id' => $data['program_kerja_id'],
            'nama_akun'        => $data['nama_akun'],

            'trial_balance_id' => $tb?->id,
        ]);

        return $kas;
    }

    public function updateKasKeluar($data, $id)
    {
        $kas = KasKeluar::findOrFail($id);

        $selisih = $data['nominal'] - $kas->nominal;

        $program = ProgramKerja::find($data['program_kerja_id']);
        $data['program_kerja'] = $program->nama_program;

        $data['transaksi'] = $data['nama_akun'];

        $kas->update($data);

        $gl = GeneralLeadge::where('kode', $kas->kode)->first();
        $coa = Coa::where('nama_akun', $data['nama_akun'])->firstOrFail();

        $tb = $this->updateTrialBalance($coa, $kas->kode, 0, $selisih);

        if ($gl) {
            $gl->update([
                'no_dokumen'       => $data['dokumen'],
                'program_kerja'    => $data['program_kerja'],
                'referensi'        => $data['ref_an'],

                // FIX
                'coa_id'           => $coa->id,

                'nominal_kredit'   => $data['nominal'],
                'saldo_awal'       => $tb->kredit ?? 0,
                'rekening_kas'     => $data['rekning_kas'],
                'dibayarkan_ke'    => $data['dibayar_ke'],
                'kode_transaksi'   => $data['transaksi'],
                'nominal_rp'       => $data['nominal'],
                'tgl'              => $data['tgl'],
                'program_kerja_id' => $data['program_kerja_id'],
                'nama_akun'        => $data['nama_akun'],
            ]);
        }

        return $kas;
    }

    public function deleteKasKeluar($id)
    {
        $kas = KasKeluar::findOrFail($id);

        $gl = GeneralLeadge::where('kode', $kas->kode)->first();

        if ($gl) {
            $coa = Coa::find($gl->coa_id);

            $this->updateTrialBalance(
                $coa,
                $kas->kode,
                0,
                -$gl->nominal_kredit
            );

            $gl->delete();
        }

        $kas->delete();
    }
}