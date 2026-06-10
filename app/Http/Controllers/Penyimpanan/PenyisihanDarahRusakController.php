<?php

namespace App\Http\Controllers\Penyimpanan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PenyisihanDarahRusakService;
use App\Models\PenyisihanDarahRusak;

class PenyisihanDarahRusakController extends Controller
{
    public function __construct(
        protected PenyisihanDarahRusakService $service
    ) {}

   
    public function index()
    {
        return view('app.penyimpanan.penyisihan_darah_rusak.index');
    }

    public function getData(Request $request)
    {
        $data = $this->service->getData($request->only([
            'search', 'status', 'tgl_dari', 'tgl_sampai', 'per_page',
        ]));

        return response()->json($data);
    }

    public function nextNomor()
    {
        return response()->json(['no_penyisihan' => $this->service->generateNomor()]);
    }

    public function cariStok(Request $request)
    {
        $request->validate(['no_stok' => 'required|string']);

        $stok = $this->service->findStok($request->no_stok);

        if (!$stok) {
            return response()->json(['message' => 'Stok tidak ditemukan atau tidak tersedia.'], 404);
        }

        return response()->json($stok->load('penerimaan'));
    }

    public function show(PenyisihanDarahRusak $penyisihanDarahRusak)
    {
        return response()->json(
            $penyisihanDarahRusak->load(['details.stokDarah', 'petugas', 'approvedBy'])
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tgl_penyisihan'         => 'required|date',
            'alasan'                 => 'required|string|max:100',
            'keterangan'             => 'nullable|string',
            'petugas_id'             => 'nullable|exists:users,id',
            'details'                => 'required|array|min:1',
            'details.*.no_stok'      => 'required|string|exists:stok_darah,no_stok',
        ]);

        try {
            $result = $this->service->store($validated);
            return response()->json(['message' => 'Data berhasil disimpan.', 'data' => $result], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function update(Request $request, PenyisihanDarahRusak $penyisihanDarahRusak)
    {
        $validated = $request->validate([
            'tgl_penyisihan'         => 'sometimes|date',
            'alasan'                 => 'sometimes|string|max:100',
            'keterangan'             => 'nullable|string',
            'details'                => 'sometimes|array|min:1',
            'details.*.no_stok'      => 'required_with:details|string|exists:stok_darah,no_stok',
        ]);

        try {
            $result = $this->service->update($penyisihanDarahRusak, $validated);
            return response()->json(['message' => 'Data berhasil diperbarui.', 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function approve(PenyisihanDarahRusak $penyisihanDarahRusak)
    {
        try {
            $result = $this->service->approve($penyisihanDarahRusak);
            return response()->json(['message' => 'Penyisihan berhasil disetujui.', 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function destroy(PenyisihanDarahRusak $penyisihanDarahRusak)
    {
        try {
            $this->service->destroy($penyisihanDarahRusak);
            return response()->json(['message' => 'Data berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}