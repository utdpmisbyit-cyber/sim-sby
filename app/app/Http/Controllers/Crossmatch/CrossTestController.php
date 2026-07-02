<?php

namespace App\Http\Controllers\Crossmatch;

use App\Http\Controllers\Controller;
use App\Models\CrossTest;
use App\Services\CrossTestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrossTestController extends Controller
{
    public function __construct(private CrossTestService $service) {}

    public function index(): View
    {
        return view('app.crossmatch.cross_test.index');
    }

    public function scan(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'no_fpup' => 'required|string|max:30',
            ]);

            $result = $this->service->findByNoFpup(trim($request->no_fpup));

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'No. FPUP tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'success'     => true,
                'fpup'        => $result['fpup'],
                'cross_tests' => $result['cross_tests'],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal: ' . collect($e->errors())->flatten()->first()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function search(Request $request): JsonResponse
    {
        $keyword = trim($request->keyword);

        $data = \DB::table('cross_tests as ct')
            ->leftJoin('permintaan_fpup as fp', 'fp.id', '=', 'ct.permintaan_fpup_id')
            ->select(
                'ct.id',
                'ct.no_fpup',
                'ct.nama_pasien',
                'ct.no_stock',
                'ct.status',
                'fp.nama_rs'
            )
            ->when($keyword, function ($q) use ($keyword) {
                $q->where(function ($x) use ($keyword) {
                    $x->where('ct.no_fpup', 'like', "%{$keyword}%")
                    ->orWhere('ct.nama_pasien', 'like', "%{$keyword}%")
                    ->orWhere('ct.no_stock', 'like', "%{$keyword}%");
                });
            })
            ->orderByDesc('ct.id')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    public function scanStock(Request $request): JsonResponse
    {
        $request->validate(['no_stock' => 'required|string|max:30']);

        $stok = $this->service->findStokByNoStock(trim($request->no_stock));
        if (!$stok) {
            return response()->json(['success' => false, 'message' => 'No. Stock tidak ditemukan di stok darah.'], 404);
        }

        return response()->json(['success' => true, 'stok' => $stok]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'permintaan_fpup_id' => 'required|exists:permintaan_fpup,id',
            'no_fpup'            => 'required|string|max:30',
            'no_stock'           => 'required|string|max:30',
            'jns_darah'          => 'nullable|string|max:50',
            'gol_rh_kantong'     => 'nullable|string|max:10',
            'tgl_produksi'       => 'nullable|date',
            'tgl_kadaluarsa'     => 'nullable|date',
            'tgl_ambil'          => 'nullable|date',
            'metode'             => 'nullable|in:Gel,Tabung,Gel + Tabung',
            'hasil_mayor'        => 'nullable|in:Compatible,Incompatible,-',
            'hasil_minor'        => 'nullable|in:Compatible,Incompatible,-',
            'hasil_autocontrol'  => 'nullable|in:Negatif,Positif,-',
            'catatan_hasil'      => 'nullable|string',
            'pemeriksa'          => 'nullable|string|max:100',
        ]);

        $ct = $this->service->saveCrossTest($validated);

        return response()->json(['success' => true, 'message' => 'Cross match berhasil disimpan.', 'cross_test' => $ct]);
    }

    /**
     * Ambil satu data cross test untuk form edit.
     */
    public function show(CrossTest $crossTest): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $crossTest]);
    }

    /**
     * Update satu data cross test.
     */
    public function update(Request $request, CrossTest $crossTest): JsonResponse
    {
        $validated = $request->validate([
            'jns_darah'         => 'nullable|string|max:50',
            'gol_rh_kantong'    => 'nullable|string|max:10',
            'tgl_produksi'      => 'nullable|date',
            'tgl_kadaluarsa'    => 'nullable|date',
            'tgl_ambil'         => 'nullable|date',
            'metode'            => 'nullable|in:Gel,Tabung,Gel + Tabung',
            'hasil_mayor'       => 'nullable|in:Compatible,Incompatible,-',
            'hasil_minor'       => 'nullable|in:Compatible,Incompatible,-',
            'hasil_autocontrol' => 'nullable|in:Negatif,Positif,-',
            'catatan_hasil'     => 'nullable|string',
            'pemeriksa'         => 'nullable|string|max:100',
        ]);

        // Hitung ulang status
        $mayor = $validated['hasil_mayor'] ?? $crossTest->hasil_mayor;
        $minor = $validated['hasil_minor'] ?? $crossTest->hasil_minor;
        if ($mayor === 'Incompatible' || $minor === 'Incompatible') {
            $validated['status'] = 'incompatible';
        } elseif ($mayor === 'Compatible' && $minor === 'Compatible') {
            $validated['status'] = 'compatible';
        }

        $crossTest->update($validated);

        return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui.', 'cross_test' => $crossTest->fresh()]);
    }

    public function destroy(CrossTest $crossTest): JsonResponse
    {
        $this->service->delete($crossTest);
        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
    }
     public function scanPetugas(Request $request): JsonResponse
    {
        $request->validate(['kode' => 'required|string|max:20']);

        $kode = trim($request->kode);

        // Cari di tabel petugas — sesuaikan kolom dengan tabel Anda
        $petugas = \DB::table('petugas')
            ->where('kode', $kode)
            ->first();

        if (!$petugas) {
            return response()->json([
                'success' => false,
                'message' => 'Petugas tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'petugas' => [
                'id'   => $petugas->id,
                'nama' => $petugas->nama, // sesuaikan nama kolom
            ],
        ]);
    }
}