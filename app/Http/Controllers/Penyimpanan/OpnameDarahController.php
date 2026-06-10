<?php

namespace App\Http\Controllers\Penyimpanan;

use App\Http\Controllers\Controller;
use App\Models\OpnameDarah;
use App\Models\BagianPetugas;
use App\Services\OpnameDarahService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class OpnameDarahController extends Controller
{
    public function __construct(protected OpnameDarahService $service) {}

 
    public function index(): View
    {
        return view('app.penyimpanan.opname_darah.index');
    }

    public function getData(Request $request): JsonResponse
    {
        $data = $this->service->getData($request->only([
            'search', 'status', 'tgl_dari', 'tgl_sampai', 'per_page',
        ]));

        return response()->json($data);
    }

    
    public function nextNomor(): JsonResponse
    {
        return response()->json(['no_opname' => $this->service->generateNoOpname()]);
    }

   
    public function cariStok(Request $request): JsonResponse
    {
        $stok = $this->service->cariStok($request->only([
            'no_stok', 'jenis_darah', 'golongan_darah', 'rhesus',
        ]));

        return response()->json($stok);
    }

    public function show(OpnameDarah $opnameDarah): JsonResponse
    {
        return response()->json($this->service->show($opnameDarah));
    }

    
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tgl_opname'         => 'required|date',
            'lokasi_opname_id'   => 'nullable|exists:bagian_petugas,id',
            'keterangan'         => 'nullable|string',
            'petugas_id'         => 'nullable|exists:petugas,id',
            'detail'             => 'nullable|array',
            'detail.*.no_stok'   => 'required|string',
            'detail.*.jumlah_fisik' => 'required|integer|min:0',
            'detail.*.keterangan'   => 'nullable|string',
        ]);

        $opname = $this->service->store($validated);

        return response()->json([
            'message' => 'Opname berhasil disimpan.',
            'data'    => $opname,
        ], 201);
    }

    public function update(Request $request, OpnameDarah $opnameDarah): JsonResponse
    {
        $validated = $request->validate([
            'tgl_opname'         => 'sometimes|date',
            'lokasi_opname_id'      => 'nullable|exists:bagian_petugas,id',
            'keterangan'         => 'nullable|string',
            'petugas_id'         => 'nullable|exists:petugas,id',
            'detail'             => 'nullable|array',
            'detail.*.no_stok'   => 'required|string',
            'detail.*.jumlah_fisik' => 'required|integer|min:0',
            'detail.*.keterangan'   => 'nullable|string',
        ]);

        $opname = $this->service->update($opnameDarah, $validated);

        return response()->json([
            'message' => 'Opname berhasil diperbarui.',
            'data'    => $opname,
        ]);
    }

   
    public function selesai(OpnameDarah $opnameDarah): JsonResponse
    {
        $opname = $this->service->selesai($opnameDarah);

        return response()->json([
            'message' => 'Opname berhasil diselesaikan.',
            'data'    => $opname,
        ]);
    }

    public function summary(OpnameDarah $opnameDarah): JsonResponse
    {
        return response()->json($this->service->getSummary($opnameDarah));
    }

    public function destroy(OpnameDarah $opnameDarah): JsonResponse
    {
        $this->service->destroy($opnameDarah);

        return response()->json(['message' => 'Opname berhasil dihapus.']);
    }

    public function cariBagian(Request $request): JsonResponse
    {
        $keyword = $request->get('q', '');
        $data = \App\Models\BagianPetugas::where('nama', 'like', "%{$keyword}%")
            ->orWhere('kode', 'like', "%{$keyword}%")
            ->select('id', 'kode', 'nama')
            ->orderBy('nama')
            ->limit(30)
            ->get();

        return response()->json($data);
    }
    public function cariPetugas(Request $request): JsonResponse
    {
        $keyword = $request->get('q', '');
        $data = \App\Models\Petugas::where('nama', 'like', "%{$keyword}%")
            ->orWhere('kode', 'like', "%{$keyword}%")
            ->select('id', 'kode', 'nama')
            ->orderBy('nama')
            ->limit(30)
            ->get();

        return response()->json($data);
    }
}