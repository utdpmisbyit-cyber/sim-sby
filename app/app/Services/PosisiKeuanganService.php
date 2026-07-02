<?php

namespace App\Services;

use App\Models\GeneralLeadge;
use Illuminate\Http\Request;

class PosisiKeuanganService
{
    // =====================================================
    // UNTUK TABEL BLADE PERTAMA
    // =====================================================
    public function searchTabel(Request $req)
    {
        $query = GeneralLeadge::query()
            ->with('coa')
            ->when($req->nama, fn($q) =>
                $q->where('nama_akun', 'like', "%{$req->nama}%")
            )
            ->when($req->kode, fn($q) =>
                $q->where('kode', 'like', "%{$req->kode}%")
            )
            ->when($req->tgl_awal && $req->tgl_akhir, fn($q) =>
                $q->whereBetween('tgl', [$req->tgl_awal, $req->tgl_akhir])
            );

        return $query->orderBy('tgl', 'asc')->get();
    }

    // =====================================================
    // UNTUK LAPORAN CHART + EXPORT
    // =====================================================
    public function searchLaporan(Request $req)
    {
        $start = $req->start ?? date('Y-01-01');
        $end   = $req->end ?? date('Y-m-d');

        return GeneralLeadge::with('coa')
            ->whereBetween('tgl', [$start, $end])
            ->orderBy('tgl', 'asc')
            ->get()
            ->map(function ($i) {
                return [
                    'tgl'            => $i->tgl,
                    'kode'           => $i->kode,
                    'nama_akun'      => $i->nama_akun,
                    'keterangan'     => $i->keterangan,
                    'nominal_debit'  => (float) $i->nominal_debit,
                    'nominal_kredit' => (float) $i->nominal_kredit,
                    'coa'            => [
                        'kd_coa'     => $i->coa->kd_coa ?? null,
                        'nama_akun'  => $i->coa->nama_akun ?? null,
                        'kategori_1' => $i->coa->kategori_1 ?? null,
                    ],
                ];
            });
    }
}