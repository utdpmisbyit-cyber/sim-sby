<?php

namespace App\Http\Controllers\Crossmatch;

use App\Http\Controllers\Controller;
use App\Models\PengembalianDarahCrossmatch;
use App\Services\PengembalianDarahCrossmatchService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PengembalianDarahCrossmatchController extends Controller
{
    public function __construct(
        protected PengembalianDarahCrossmatchService $service
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'tanggal_dari', 'tanggal_sampai', 'status_kembali']);
        $list    = $this->service->getList($filters, 15);
        return view('app.crossmatch.pengembalian_darah.index', compact('list', 'filters'));
    }

    public function create(): View
    {
        $nomorBaru = PengembalianDarahCrossmatch::generateNomor();
        return view('app.crossmatch.pengembalian_darah.form', [
            'pengembalian' => null,
            'nomorBaru'    => $nomorBaru,
            'isEdit'       => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal_kembali'          => 'required|date',
            'no_fpup'                  => 'nullable|string|max:50',
            'tgl_fpup'                 => 'nullable|date',
            'no_stock'                 => 'nullable|string|max:50',
            'kode_petugas'             => 'nullable|string|max:20',
            'nama_petugas'             => 'nullable|string|max:100',
            'kode_rumah_sakit'         => 'nullable|string|max:20',
            'nama_rumah_sakit'         => 'nullable|string|max:200',
            'alasan_kembali'           => 'nullable|string|max:500',
            'status_kembali'           => 'required|in:Baik,Rusak,Kadaluarsa',
            'tgl_pemberian'            => 'nullable|date',
            'umur_hari_pemberian'      => 'nullable|integer|min:0',
            'yang_mengembalikan'       => 'nullable|string|max:100',
            'keterangan'               => 'nullable|string',
            'details'                  => 'nullable|array',
            'details.*.no_stock'       => 'required|string|max:50',
            'details.*.jenis_darah'    => 'nullable|string|max:50',
            'details.*.gol_darah'      => 'nullable|string|max:10',
            'details.*.rhesus'         => 'nullable|string|max:10',
            'details.*.sts'            => 'nullable|string|max:50',  // ← diperbesar
            'details.*.status_kembali' => 'nullable|in:Baik,Rusak,Kadaluarsa',
            'details.*.alasan_kembali' => 'nullable|string|max:500',
            'details.*.tgl_aftap'      => 'nullable|date',
            'details.*.kadaluarsa'     => 'nullable|date',
            'details.*.jumlah'         => 'nullable|integer|min:1',
            'details.*.keterangan'     => 'nullable|string',
        ]);

        try {
            $this->service->store($validated);
            // ✅ FIX: route name yang benar
            return redirect()
                ->route('crossmatch.pengembalian_darah.index')
                ->with('success', 'Data pengembalian darah berhasil disimpan.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function show(PengembalianDarahCrossmatch $pengembalianDarah): View
    {
        $pengembalianDarah->load('details');
        return view('app.crossmatch.pengembalian_darah.show', ['pengembalian' => $pengembalianDarah]);
    }

    public function edit(PengembalianDarahCrossmatch $pengembalianDarah): View
    {
        $pengembalianDarah->load('details');
        return view('app.crossmatch.pengembalian_darah.form', [
            'pengembalian' => $pengembalianDarah,
            'nomorBaru'    => $pengembalianDarah->nomor_kembali,
            'isEdit'       => true,
        ]);
    }

    public function update(Request $request, PengembalianDarahCrossmatch $pengembalianDarah): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal_kembali'          => 'required|date',
            'no_fpup'                  => 'nullable|string|max:50',
            'tgl_fpup'                 => 'nullable|date',
            'no_stock'                 => 'nullable|string|max:50',
            'kode_petugas'             => 'nullable|string|max:20',
            'nama_petugas'             => 'nullable|string|max:100',
            'kode_rumah_sakit'         => 'nullable|string|max:20',
            'nama_rumah_sakit'         => 'nullable|string|max:200',
            'alasan_kembali'           => 'nullable|string|max:500',
            'status_kembali'           => 'required|in:Baik,Rusak,Kadaluarsa',
            'tgl_pemberian'            => 'nullable|date',
            'umur_hari_pemberian'      => 'nullable|integer|min:0',
            'yang_mengembalikan'       => 'nullable|string|max:100',
            'keterangan'               => 'nullable|string',
            'details'                  => 'nullable|array',
            'details.*.no_stock'       => 'required|string|max:50',
            'details.*.jenis_darah'    => 'nullable|string|max:50',
            'details.*.gol_darah'      => 'nullable|string|max:10',
            'details.*.rhesus'         => 'nullable|string|max:10',
            'details.*.sts'            => 'nullable|string|max:50',
            'details.*.status_kembali' => 'nullable|in:Baik,Rusak,Kadaluarsa',
            'details.*.alasan_kembali' => 'nullable|string|max:500',
            'details.*.tgl_aftap'      => 'nullable|date',
            'details.*.kadaluarsa'     => 'nullable|date',
            'details.*.jumlah'         => 'nullable|integer|min:1',
            'details.*.keterangan'     => 'nullable|string',
        ]);

        try {
            $this->service->update($pengembalianDarah, $validated);
            // ✅ FIX: route name yang benar
            return redirect()
                ->route('crossmatch.pengembalian_darah.index')
                ->with('success', 'Data pengembalian darah berhasil diperbarui.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }
    }

    public function destroy(PengembalianDarahCrossmatch $pengembalianDarah): RedirectResponse
    {
        try {
            $this->service->destroy($pengembalianDarah);
            // ✅ FIX: route name yang benar
            return redirect()
                ->route('crossmatch.pengembalian_darah.index')
                ->with('success', 'Data berhasil dihapus.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    // ─── Scan Endpoints (AJAX) ────────────────────────────────────────────────

    public function scanFpup(Request $request): JsonResponse
    {
        try {
            $request->validate(['no_fpup' => 'required|string|max:50']);
            $result = $this->service->scanFpup($request->no_fpup);
            return response()->json($result, $result['found'] ? 200 : 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['found' => false, 'message' => collect($e->errors())->flatten()->first()], 422);
        } catch (\Throwable $e) {
            return response()->json(['found' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function scanStock(Request $request): JsonResponse
    {
        try {
            $request->validate(['no_stock' => 'required|string|max:50']);
            $result = $this->service->scanStock($request->no_stock);
            return response()->json($result, $result['found'] ? 200 : 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['found' => false, 'message' => collect($e->errors())->flatten()->first()], 422);
        } catch (\Throwable $e) {
            return response()->json(['found' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function scanPetugas(Request $request): JsonResponse
    {
        try {
            $request->validate(['kode_petugas' => 'required|string|max:20']);
            $result = $this->service->scanPetugas($request->kode_petugas);
            return response()->json($result, $result['found'] ? 200 : 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['found' => false, 'message' => collect($e->errors())->flatten()->first()], 422);
        } catch (\Throwable $e) {
            return response()->json(['found' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function debugTables(): JsonResponse
    {
        try {
            $rows   = DB::select('SHOW TABLES');
            $result = [];
            foreach ($rows as $row) {
                $row    = (array) $row;
                $name   = array_values($row)[0];
                $result[$name] = Schema::getColumnListing($name);
            }
            return response()->json([
                'database' => config('database.connections.mysql.database'),
                'tables'   => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}