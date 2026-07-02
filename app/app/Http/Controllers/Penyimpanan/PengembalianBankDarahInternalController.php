<?php

namespace App\Http\Controllers\Penyimpanan;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\PengembalianBankDarahInternalService;
use App\Models\PengembalianBankDarahInternal;
use App\Models\PengembalianBankDarahInternalDetail;
use App\Models\BankDarah;
use App\Models\Petugas;

class PengembalianBankDarahInternalController extends Controller
{
    public function __construct(
        protected PengembalianBankDarahInternalService $service
    ) {}

    // ─── Index ────────────────────────────────────────────────────────────────

    public function index()
    {
        return view('app.penyimpanan.pengembalian_bank_darah_internal.index');
    }

    // ─── Data (JSON untuk tabel manual + stats) ────────────────────────────────

    public function getData(Request $request): JsonResponse
    {
        $query = PengembalianBankDarahInternal::with([
            'bankDarahAsal', 'bankDarahTujuan', 'petugasKembali', 'petugasTerima',
        ])->withCount('details');

        if ($request->filled('tgl_dari')) {
            $query->whereDate('tgl_pengembalian', '>=', $request->tgl_dari);
        }
        if ($request->filled('tgl_sampai')) {
            $query->whereDate('tgl_pengembalian', '<=', $request->tgl_sampai);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('no_pengembalian', 'like', "%{$s}%")
                  ->orWhereHas('bankDarahAsal',   fn($qq) => $qq->where('nama', 'like', "%{$s}%"))
                  ->orWhereHas('bankDarahTujuan', fn($qq) => $qq->where('nama', 'like', "%{$s}%"));
            });
        }

        $total = $query->count();

        // Pagination manual
        $perPage = (int) ($request->length ?? 10);
        $page    = (int) ($request->page   ?? 1);
        $offset  = ($page - 1) * $perPage;

        $rows = $query->latest()->skip($offset)->take($perPage)->get();

        // Stats agregat (tidak bergantung filter agar selalu tampil)
        $bulanIni = PengembalianBankDarahInternal::whereMonth('tgl_pengembalian', now()->month)
            ->whereYear('tgl_pengembalian', now()->year)->count();

        $hariIni = PengembalianBankDarahInternal::whereDate('tgl_pengembalian', today())->count();

        $totalKantong = PengembalianBankDarahInternalDetail::count();

        return response()->json([
            'draw'            => (int) $request->draw,
            'recordsTotal'    => PengembalianBankDarahInternal::count(),
            'recordsFiltered' => $total,
            'bulan_ini'       => $bulanIni,
            'hari_ini'        => $hariIni,
            'total_kantong'   => $totalKantong,
            'data' => $rows->map(fn($r) => [
                'id'                 => $r->id,
                'no_pengembalian'    => $r->no_pengembalian,
                'tgl_pengembalian'   => $r->tgl_pengembalian_formatted,
                'bank_darah_asal'    => $r->bankDarahAsal?->nama,
                'bank_darah_tujuan'  => $r->bankDarahTujuan?->nama,
                'petugas_kembali'    => $r->petugasKembali?->nama,
                'petugas_terima'     => $r->petugasTerima?->nama,
                'jumlah_item'        => $r->details_count,
                'status'             => $r->status,
            ])
        ]);
    }

    // ─── Show ─────────────────────────────────────────────────────────────────

    public function show(int $id): JsonResponse
    {
        $data = PengembalianBankDarahInternal::with([
            'details.stokDarah',
            'bankDarahAsal',
            'bankDarahTujuan',
            'petugasKembali',
            'petugasTerima',
        ])->findOrFail($id);

        return response()->json($data);
    }

    // ─── Next Nomor ───────────────────────────────────────────────────────────

    public function nextNomor(): JsonResponse
    {
        try {
            return response()->json([
                'nomor' => $this->service->generateNomor()
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ], 500);
        }
    }

    // ─── Cari Stok ────────────────────────────────────────────────────────────

    public function cariStok(Request $request): JsonResponse
    {
        $request->validate(['no_stok' => 'required|string']);
        $stok = $this->service->cariStok($request->no_stok);

        if (! $stok) {
            return response()->json([
                'message' => 'Stok tidak ditemukan atau tidak dalam status keluar / dipakai.',
            ], 404);
        }

        return response()->json($stok);
    }

    // ─── Store ────────────────────────────────────────────────────────────────

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'tgl_pengembalian'          => 'required|date',
            'bank_darah_asal_id'        => 'nullable|integer',
            'bank_darah_tujuan_id'      => 'nullable|integer',
            'petugas_kembali_id'        => 'nullable|integer',
            'petugas_terima_id'         => 'nullable|integer',
            'details'                   => 'required|array|min:1',
            'details.*.no_stok'         => 'required|string',
            'details.*.status_kembali'  => 'required|string',
        ]);

        try {
            $result = $this->service->store($request->all());
            return response()->json([
                'message' => 'Pengembalian bank darah internal berhasil disimpan.',
                'data'    => $result,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
        }
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'tgl_pengembalian'          => 'required|date',
            'bank_darah_asal_id'        => 'nullable|integer',
            'bank_darah_tujuan_id'      => 'nullable|integer',
            'petugas_kembali_id'        => 'nullable|integer',
            'petugas_terima_id'         => 'nullable|integer',
            'details'                   => 'required|array|min:1',
            'details.*.no_stok'         => 'required|string',
            'details.*.status_kembali'  => 'required|string',
        ]);

        $pengembalian = PengembalianBankDarahInternal::findOrFail($id);

        try {
            $result = $this->service->update($pengembalian, $request->all());
            return response()->json([
                'message' => 'Pengembalian bank darah internal berhasil diperbarui.',
                'data'    => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Gagal update: ' . $e->getMessage()], 500);
        }
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(int $id): JsonResponse
    {
        $pengembalian = PengembalianBankDarahInternal::findOrFail($id);

        try {
            $this->service->destroy($pengembalian);
            return response()->json(['message' => 'Data berhasil dihapus.']);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Gagal hapus: ' . $e->getMessage()], 500);
        }
    }

    // ─── Search Bank Darah / Unit Kerja ────────────────────────────────────────

    public function searchBankDarah(Request $request): JsonResponse
    {
        $q = $request->get('q', '');
        $results = BankDarah::where('nama', 'like', "%{$q}%")
            ->orWhere('kode', 'like', "%{$q}%")
            ->limit(10)
            ->get(['id', 'kode', 'nama']);

        return response()->json($results);
    }

    // ─── Search Petugas ─────────────────────────────────────────────────────────

    public function searchPetugas(Request $request): JsonResponse
    {
        $q = $request->get('q', '');
        $results = Petugas::where('nama', 'like', "%{$q}%")
            ->orWhere('kode', 'like', "%{$q}%")
            ->limit(10)
            ->get(['id', 'kode', 'nama']);

        return response()->json($results);
    }
}