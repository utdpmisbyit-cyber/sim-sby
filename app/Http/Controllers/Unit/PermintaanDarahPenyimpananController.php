<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use App\Models\PermintaanDarahPenyimpanan;
use App\Services\PermintaanDarahPenyimpananService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\BankDarah;
use App\Models\JenisDarah;
use App\Models\PermintaanFpup;

class PermintaanDarahPenyimpananController extends Controller
{
    public function __construct(
        private readonly PermintaanDarahPenyimpananService $service
    ) {}

   
    public function index(Request $request): View
    {
        $filters = $request->only(['status', 'tanggal_minta', 'search']);
        $rows    = $this->service->index($filters);

        return view('app.unit.bank_darah.permintaan_penyimpanan.index', [
            'rows'       => $rows,
            'filters'    => $filters,
            'statusList' => PermintaanDarahPenyimpanan::statusLabel(),
        ]);
    }

    
    public function store(Request $request): JsonResponse
    {

        try {
            // FIX: 'detail' dikirim sebagai JSON string dari JS, parse dulu
            $detailRaw = $request->input('detail');
            $detail    = is_string($detailRaw) ? json_decode($detailRaw, true) : $detailRaw;

            $request->merge(['detail' => $detail]);

            $request->validate([
                'bank_darah_kode'         => 'required|string|max:10',
                'bank_darah_nama'         => 'required|string|max:200',
                'petugas_kode'            => 'nullable|string|max:20',
                'petugas_nama'            => 'nullable|string|max:200',
                'tanggal_minta'           => 'required|date',
                'detail'                  => 'required|array|min:1',
                'detail.*.jenis_darah'    => 'required|string',
                'detail.*.golongan_darah' => 'required|in:A,B,AB,O',
                'detail.*.rhesus'         => 'required|in:Positif,Negatif',
                'detail.*.jumlah_kantong' => 'required|integer|min:1',
                'detail.*.jumlah_cc'      => 'nullable|integer|min:0',
                'detail.*.tanggal_perlu'  => 'nullable|date',
            ]);

            $permintaan = $this->service->store($request->all());
            
            return response()->json([
                'success' => true,
                'message' => "Permintaan {$permintaan->no_permintaan} berhasil disimpan.",
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(' ', $e->validator->errors()->all()),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $permintaan = $this->service->find($id);
            // FIX: load('details') konsisten dengan nama relasi di model
            return response()->json([
                'success' => true,
                'data'    => $permintaan->load('details'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            // FIX: parse JSON string dari JS
            $detailRaw = $request->input('detail');
            $detail    = is_string($detailRaw) ? json_decode($detailRaw, true) : $detailRaw;
            $request->merge(['detail' => $detail]);

            $request->validate([
                'bank_darah_kode'         => 'required|string|max:10',
                'bank_darah_nama'         => 'required|string|max:200',
                'tanggal_minta'           => 'required|date',
                'detail'                  => 'required|array|min:1',
                'detail.*.jenis_darah'    => 'required|string',
                'detail.*.golongan_darah' => 'required|in:A,B,AB,O',
                'detail.*.rhesus'         => 'required|in:Positif,Negatif',
                'detail.*.jumlah_kantong' => 'required|integer|min:1',
                'detail.*.jumlah_cc'      => 'nullable|integer|min:0',
                'detail.*.tanggal_perlu'  => 'nullable|date',
            ]);

            $permintaan = $this->service->update($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => "Permintaan {$permintaan->no_permintaan} berhasil diperbarui.",
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(' ', $e->validator->errors()->all()),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:permintaan,proses,selesai,batal',
        ]);

        $permintaan = $this->service->updateStatus($id, $request->status);

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui.',
            'status'  => $permintaan->status_label,
        ]);
    }
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->destroy($id);
            return response()->json([
                'success' => true,
                'message' => 'Permintaan berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function nextNoPermintaan(): JsonResponse
    {
        return response()->json([
            'no_permintaan' => PermintaanDarahPenyimpanan::generateNomor(),
        ]);
    }

    public function searchBankDarah(Request $request): JsonResponse
    {
        $keyword = $request->get('q', '');

        $results = BankDarah::query()
            ->when($keyword, function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                ->orWhere('kode', 'like', "%{$keyword}%");
            })
            ->orderBy('nama')
            ->limit(50)
            ->get(['kode', 'nama']);

        return response()->json($results);
    }
     public function getJenisDarah(): JsonResponse
    {
        $data = JenisDarah::orderBy('nama_pendek')
            ->get(['kode', 'nama_pendek']);

        return response()->json($data);
    }
     public function searchFpup(Request $request): JsonResponse
    {
        $q = $request->get('q', '');

        $data = PermintaanFpup::query()
            ->when($q, function ($query) use ($q) {
                $query->where('no_fpup', 'like', "%{$q}%")
                    ->orWhere('nama_pasien', 'like', "%{$q}%");
            })
            ->limit(20)
            ->get();

        return response()->json($data);
    }
     public function detailFpup($noFpup): JsonResponse
    {
        $fpup = PermintaanFpup::where('no_fpup', $noFpup)
            ->first();

        if (!$fpup) {
            return response()->json([
                'success' => false,
                'message' => 'Data FPUP tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $fpup
        ]);
    }
}