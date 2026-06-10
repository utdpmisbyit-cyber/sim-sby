<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\StokDarahService;
use App\Services\PenerimaanProlisPenyimpananService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class StokDarahController extends Controller
{
    public function __construct(
        protected StokDarahService $stokService,
        protected PenerimaanProlisPenyimpananService $penerimaanService
    ) {}

    public function index(): \Illuminate\View\View
    {
        return view('app.penyimpanan.stok.index', [
            'jenisOptions'    => $this->penerimaanService->getJenisOptions(),
            'golonganOptions' => $this->penerimaanService->getGolonganDarahOptions(),
            'rhesusOptions'   => $this->penerimaanService->getRhesusOptions(),
            'ruanganOptions'  => $this->penerimaanService->getRuanganOptions(),
        ]);
    }

    public function getData(Request $request): JsonResponse
    {
        $items = $this->stokService->getAll($request->only([
            'jenis_darah', 'golongan_darah', 'rhesus', 'ruang', 'status_stok', 'no_stok',
        ]));

        return response()->json([
            'data'   => $this->formatRows($items),
            'jumlah' => $items->count(),
        ]);
    }

    public function transaksi(string $noStok): JsonResponse
    {
        $items = $this->stokService->getTransaksiByNoStok($noStok);

        return response()->json([
            'data'   => $items->map(fn($t) => [
                'id'           => $t->id,
                'jenis'        => $t->jenis,
                'jumlah'       => $t->jumlah,
                'no_referensi' => $t->no_referensi,
                'sumber'       => $t->sumber,
                'keterangan'   => $t->keterangan,
                'petugas'      => $t->petugas?->name ?? '-',
                'created_at'   => Carbon::parse($t->created_at)->format('d/m/Y H:i'),
            ])->toArray(),
            'jumlah' => $items->count(),
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        $ruang = $request->get('ruang', 'A-1200');

        return response()->json(
            $this->stokService->getSummaryByRuang($ruang)
        );
    }

    private function formatRows($items): array
    {
        return $items->map(fn($r) => [
            'id'             => $r->id,
            'no_stok'        => $r->no_stok,
            'jenis_darah'    => $r->jenis_darah,
            'golongan_darah' => $r->golongan_darah,
            'rhesus'         => $r->rhesus,
            'ruang'          => $r->ruang,
            'tgl_expired'    => $r->tgl_expired?->format('d/m/Y') ?? '-',
            'jumlah_masuk'   => $r->jumlah_masuk,
            'jumlah_keluar'  => $r->jumlah_keluar,
            'jumlah_kembali' => $r->jumlah_kembali,
            'saldo'          => $r->saldo,
            'status_stok'    => $r->status_stok,
            'ml'             => $r->ml,
        ])->toArray();
    }
}