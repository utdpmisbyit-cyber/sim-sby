<?php

namespace App\Http\Controllers\Penyimpanan;

use App\Http\Controllers\Controller;
use App\Models\FraksionasiDarah;
use App\Models\PendataanKantong;
use App\Services\FraksionasiDarahService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class FraksionasiDarahController extends Controller
{
    public function __construct(protected FraksionasiDarahService $service) {}

    // ─── Index ──────────────────────────────────────────────────────────────────

    public function index()
    {
        return view('app.penyimpanan.fraksionasi_darah.index');
    }

    // ─── DataTable ──────────────────────────────────────────────────────────────

    public function getData(Request $request): JsonResponse
    {
        $query = $this->service->getData($request->only([
            'status', 'jenis_darah', 'golongan_darah',
            'tgl_dari', 'tgl_sampai', 'search',
        ]));

        $perPage = (int) $request->get('per_page', 500);
        $rows    = $query->limit($perPage)->get()->map(fn ($r) => [
            'id'               => $r->id,
            'no_fraksionasi'   => $r->no_fraksionasi,
            'no_transaksi'     => $r->no_transaksi,
            'no_stok'          => $r->no_stok,
            'no_kantong'       => $r->no_kantong,
            'jenis_darah'      => $r->jenis_darah,
            'golongan_darah'   => $r->golongan_darah,
            'rhesus'           => $r->rhesus,
            'ukuran_kantong'   => $r->ukuran_kantong,
            'suhu_box'         => $r->suhu_box,
            'tgl_dropping'     => $r->tgl_dropping?->format('Y-m-d H:i:s'),
            'tgl_produksi'     => $r->tgl_produksi?->format('Y-m-d H:i:s'),
            'tgl_kadaluarsa'   => $r->tgl_kadaluarsa?->format('Y-m-d H:i:s'),
            'nomor_rak'        => $r->nomor_rak,
            'nomor_box'        => $r->nomor_box,
            'status'           => $r->status,
            'keterangan'       => $r->keterangan,
            'petugas_nama'     => $r->petugas?->nama,
            'petugas'          => $r->petugas ? ['id' => $r->petugas->id, 'nama' => $r->petugas->nama, 'kode' => $r->petugas->kode] : null,
        ]);

        return response()->json($rows);
    }

    // ─── Show ───────────────────────────────────────────────────────────────────

    public function show(FraksionasiDarah $fraksionasiDarah): JsonResponse
    {
        return response()->json(
            $fraksionasiDarah->load(['petugas', 'stokDarah', 'pendataanKantong'])
        );
    }

    // ─── Store ──────────────────────────────────────────────────────────────────

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'no_stok'              => 'required|string|exists:stok_darah,no_stok',
            'pendataan_kantong_id' => 'nullable|exists:pendataan_kantong,id',
            'jenis_darah'          => 'nullable|string',
            'golongan_darah'       => 'nullable|string',
            'rhesus'               => 'nullable|string',
            'no_kantong'           => 'nullable|string',
            'ukuran_kantong'       => 'nullable|in:350,450,1000',
            'jenis_kantong'        => 'nullable|string',
            'tipe_kantong'         => 'nullable|string',
            'merk'                 => 'nullable|string',
            'suhu_box'             => 'nullable|integer',
            'tgl_dropping'         => 'nullable|date',
            'tgl_produksi'         => 'nullable|date',
            'tgl_kadaluarsa'       => 'nullable|date',
            'nomor_rak'            => 'nullable|string',
            'nomor_box'            => 'nullable|string',
            'keterangan'           => 'nullable|string',
        ]);

        try {
            $fraksionasi = $this->service->store($validated);

            return response()->json([
                'success' => true,
                'message' => 'Fraksionasi darah berhasil disimpan.',
                'data'    => $fraksionasi,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    // ─── Update ─────────────────────────────────────────────────────────────────

    public function update(Request $request, FraksionasiDarah $fraksionasiDarah): JsonResponse
    {
        $validated = $request->validate([
            'pendataan_kantong_id' => 'nullable|exists:pendataan_kantong,id',
            'ukuran_kantong'       => 'nullable|in:350,450,1000',
            'jenis_kantong'        => 'nullable|string',
            'tipe_kantong'         => 'nullable|string',
            'merk'                 => 'nullable|string',
            'suhu_box'             => 'nullable|integer',
            'tgl_dropping'         => 'nullable|date',
            'tgl_produksi'         => 'nullable|date',
            'tgl_kadaluarsa'       => 'nullable|date',
            'nomor_rak'            => 'nullable|string',
            'nomor_box'            => 'nullable|string',
            'keterangan'           => 'nullable|string',
            'status'               => 'nullable|in:proses,selesai,batal',
        ]);

        try {
            $fraksionasi = $this->service->update($fraksionasiDarah, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Fraksionasi darah berhasil diperbarui.',
                'data'    => $fraksionasi,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    // ─── Selesai ────────────────────────────────────────────────────────────────

    public function selesai(FraksionasiDarah $fraksionasiDarah): JsonResponse
    {
        if ($fraksionasiDarah->status !== 'proses') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya data dengan status proses yang dapat diselesaikan.',
            ], 422);
        }

        try {
            $this->service->selesai($fraksionasiDarah);

            return response()->json([
                'success' => true,
                'message' => 'Fraksionasi darah berhasil diselesaikan.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    // ─── Destroy ────────────────────────────────────────────────────────────────

    public function destroy(FraksionasiDarah $fraksionasiDarah): JsonResponse
    {
        try {
            $this->service->destroy($fraksionasiDarah);

            return response()->json([
                'success' => true,
                'message' => 'Fraksionasi darah berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function nextNomor(): JsonResponse
    {
        return response()->json([
            'no_fraksionasi' => $this->service->generateNoFraksionasi(),
            'no_transaksi'   => $this->service->generateNoTransaksi(),
        ]);
    }

    public function cariStok(Request $request): JsonResponse
    {
        try {
            $keyword = $request->get('q', '');
            if (strlen($keyword) < 2) {
                return response()->json([]);
            }
            $results = $this->service->cariStok($keyword);
            return response()->json($results);
        } catch (\Exception $e) {
            \Log::error('cariStok controller error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'error'   => true,
                'message' => 'Gagal mencari stok: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function searchPetugas(Request $request): JsonResponse
    {
        try {
            $q = trim($request->get('q', ''));

            $list = \App\Models\Petugas::where(function ($query) use ($q) {
                    $query->where('kode', 'like', "%$q%")
                        ->orWhere('nama', 'like', "%$q%");
                })
                ->limit(10)
                ->get(['id', 'kode', 'nama']);

            return response()->json($list);

        } catch (\Exception $e) {
            \Log::error('searchPetugas error: ' . $e->getMessage());
            return response()->json([
                'error'   => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getSummary(): JsonResponse
    {
        return response()->json($this->service->getSummary());
    }

    public function getKantong(Request $request): JsonResponse
    {
        $q = $request->get('q', '');

        $kantong = PendataanKantong::where('status', 'aktif')
            ->where(function ($query) use ($q) {
                $query->where('kode', 'like', "%$q%")
                      ->orWhere('merk_kantong', 'like', "%$q%")
                      ->orWhere('jenis_kantong', 'like', "%$q%");
            })
            ->limit(20)
            ->get(['id', 'kode', 'merk_kantong', 'jenis_kantong', 'type_kantong', 'ukuran']);

        return response()->json($kantong);
    }
}