<?php

namespace App\Http\Controllers\Referal;

use App\Http\Controllers\Controller;
use App\Models\CrossTestReferal;
use App\Services\CrossTestReferalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrossTestReferalController extends Controller
{
    public function __construct(private CrossTestReferalService $service) {}

    public function index(): View
    {
        return view('app.referal.cross_test.index');
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
                    'message' => 'No. FPUP Referal tidak ditemukan.',
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
        // PERBAIKAN: sebelumnya $request->keyword dibaca tapi front-end (JS)
        // tidak pernah mengirim parameter ini sehingga selalu null.
        // JS sudah diperbaiki untuk mengirim ?keyword=... pada query string.
        $keyword = trim((string) $request->query('keyword', ''));

        $data = \DB::table('cross_tests_referal as ct')
            ->leftJoin('permintaan_fpup_referal as fp', 'fp.id', '=', 'ct.permintaan_fpup_referal_id')
            ->select(
                'ct.id',
                'ct.no_fpup',
                'ct.nama_pasien',
                'ct.no_stock',
                'ct.status',
                'fp.nama_rs'
            )
            ->when($keyword !== '', function ($q) use ($keyword) {
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
            'permintaan_fpup_referal_id'        => 'required|exists:permintaan_fpup_referal,id',
            'permintaan_fpup_referal_detail_id' => 'nullable|exists:permintaan_fpup_referal_detail,id',
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

        return response()->json(['success' => true, 'message' => 'Cross match referal berhasil disimpan.', 'cross_test' => $ct]);
    }

    /**
     * Ambil satu data cross test referal untuk form edit.
     */
    public function show(CrossTestReferal $crossTestReferal): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $crossTestReferal]);
    }

    /**
     * Update satu data cross test referal.
     */
    public function update(Request $request, CrossTestReferal $crossTestReferal): JsonResponse
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
        $mayor = $validated['hasil_mayor'] ?? $crossTestReferal->hasil_mayor;
        $minor = $validated['hasil_minor'] ?? $crossTestReferal->hasil_minor;
        if ($mayor === 'Incompatible' || $minor === 'Incompatible') {
            $validated['status'] = 'incompatible';
        } elseif ($mayor === 'Compatible' && $minor === 'Compatible') {
            $validated['status'] = 'compatible';
        }

        $crossTestReferal->update($validated);

        return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui.', 'cross_test' => $crossTestReferal->fresh()]);
    }

    public function destroy(CrossTestReferal $crossTestReferal): JsonResponse
    {
        $this->service->delete($crossTestReferal);
        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
    }

    public function scanPetugas(Request $request): JsonResponse
    {
        $request->validate(['kode' => 'required|string|max:20']);

        $kode = trim($request->kode);

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
                'nama' => $petugas->nama,
            ],
        ]);
    }
}