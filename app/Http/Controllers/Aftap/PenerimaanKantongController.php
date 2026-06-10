<?php

namespace App\Http\Controllers\Aftap;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\PenerimaanKantongService;

class PenerimaanKantongController extends Controller
{
    public function __construct(protected PenerimaanKantongService $service) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $mode = $request->query('mode');

            if ($mode === 'detail') {
                $detail = $this->service->getDetailWithStatus((int) $request->id);
                return response()->json(['data' => $detail]);
            }

            if ($mode === 'stok') {
                return response()->json($this->service->getStokSummary((int) $request->id));
            }

            // mode === 'history'
            $q = \App\Models\PenerimaanKantong::withCount('detail')
                ->when($request->dari,    fn($q) => $q->whereDate('tanggal', '>=', $request->dari))
                ->when($request->sampai,  fn($q) => $q->whereDate('tanggal', '<=', $request->sampai))
                ->when($request->keyword, fn($q) => $q->where(function ($q2) use ($request) {
                    $q2->where('no_transaksi', 'like', "%{$request->keyword}%")
                       ->orWhere('no_keluar',  'like', "%{$request->keyword}%");
                }))
                ->latest();

            $per   = (int) $request->query('per', 10);
            $page  = (int) $request->query('page', 1);
            $total = $q->count();
            $data  = $q->skip(($page - 1) * $per)->take($per)->get();

            $data->each(function ($row) {
                $summary = $this->service->getStokSummary($row->id);
                $row->total_terima   = $summary['total_terima'];
                $row->sudah_sample   = $summary['sudah_sample'];
                $row->sudah_serologi = $summary['sudah_serologi'];
                $row->sisa_stok      = $summary['sisa_stok'];
            });

            return response()->json(['total' => $total, 'data' => $data]);
        }

        $no_transaksi = $this->service->generateNo();
        return view('app.aftap.penerimaan_kantong.index', compact('no_transaksi'));
    }

    /** Scan kantong */
    public function scan(Request $request): JsonResponse
    {
        try {
            $data = $this->service->getKantongByScan($request->no_kantong);
            return response()->json(['status' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /** Jumlah kantong berdasarkan no_keluar */
    public function getJumlah(Request $request): JsonResponse
    {
        $jumlah = $this->service->getJumlahKirim($request->input('no_keluar'));
        return response()->json(['jumlah_kirim' => $jumlah]);
    }

    /**
     * Autocomplete No Gudang Keluar
     * GET /penerimaan/search-no-keluar?q=...
     */
    public function searchNoKeluar(Request $request): JsonResponse
    {
        $results = $this->service->searchNoKeluar($request->input('q', ''));
        return response()->json($results);
    }

    /**
     * Autocomplete No Permintaan
     * GET /penerimaan/search-no-permintaan?q=...
     */
    public function searchNoPermintaan(Request $request): JsonResponse
    {
        $results = $this->service->searchNoPermintaan($request->input('q', ''));
        return response()->json($results);
    }

    /**
     * Ambil semua kantong by no_keluar (untuk auto-fill tabel)
     * POST /penerimaan/kantong-by-no-keluar
     */
    public function kantongByNoKeluar(Request $request): JsonResponse
    {
        try {
            $items = $this->service->getKantongByNoKeluar($request->input('no_keluar'));
            return response()->json(['status' => true, 'data' => $items, 'jumlah' => count($items)]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Ambil semua kantong by no_permintaan (untuk auto-fill tabel)
     * POST /penerimaan/kantong-by-no-permintaan
     */
    public function kantongByNoPermintaan(Request $request): JsonResponse
    {
        try {
            $items = $this->service->getKantongByNoPermintaan($request->input('no_permintaan'));
            return response()->json(['status' => true, 'data' => $items, 'jumlah' => count($items)]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /** Simpan penerimaan */
    public function store(Request $request): JsonResponse
    {
        $data = $request->json()->all();
        $request->merge($data);
        $request->validate([
            'tanggal'   => 'required',
            'no_keluar' => 'required',
            'items'     => 'required|array|min:1',
        ]);

        try {
            $this->service->simpan($request->all());
            return response()->json(['status' => true, 'msg' => 'Berhasil disimpan']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }


    /** Stok summary satu transaksi (dipanggil via AJAX dari tombol di riwayat) */
    public function stok(int $id): JsonResponse
    {
        try {
            $summary = $this->service->getStokSummary($id);
            return response()->json(['status' => true, ...$summary]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
}