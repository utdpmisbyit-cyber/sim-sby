<?php

namespace App\Http\Controllers\Aftap;

use App\Http\Controllers\Controller;
use App\Models\PengirimanSample;
use App\Models\PengirimanSampleDetail;
use App\Services\PengirimanSampleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PengirimanSampleController extends Controller
{
    public function __construct(protected PengirimanSampleService $service) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $mode = $request->query('mode');

            if ($mode === 'detail') {
                $detail = PengirimanSampleDetail::where('pengiriman_sample_id', $request->id)
                    ->orderBy('urut')
                    ->get();
                return response()->json(['data' => $detail]);
            }

            $result = $this->service->getList(
                $request->only(['dari', 'sampai', 'keyword']),
                (int) $request->query('page', 1),
                (int) $request->query('per', 10)
            );
            return response()->json($result);
        }

        $no_fpd       = $this->service->generateNoFpd();
        $petugas_nama = auth()->user()->name ?? auth()->user()->nama ?? '';

        return view('app.aftap.pengiriman_sample.index', compact('no_fpd', 'petugas_nama'));
    }

    public function scan(Request $request): JsonResponse
    {
        try {
            $data = $this->service->getKantongByScan($request->no_kantong);
            return response()->json(['status' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 422);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'no_fpd'      => 'required',
            'tanggal_fpd' => 'required|date',
            'items'       => 'required|array|min:1',
        ]);

        try {
            $header = $this->service->simpan($request->json()->all());
            return response()->json([
                'status' => true,
                'msg'    => 'Berhasil disimpan',
                'id'     => $header->id,
                'no_fpd' => $header->no_fpd,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    public function kirimFpd(Request $request, PengirimanSample $pengirimanSample): JsonResponse
    {
        try {
            $serologi = $this->service->kirimFpd(
                $pengirimanSample->id,
                $request->only(['pengirim_id', 'penerima_id', 'dokumen'])
            );
            return response()->json([
                'status'       => true,
                'msg'          => 'FPD berhasil dikirim ke serologi',
                'serologi_id'  => $serologi->id,
                'kode'         => $serologi->kode,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    public function show(PengirimanSample $pengirimanSample): JsonResponse
    {
        return response()->json([
            'header' => $pengirimanSample,
            'detail' => $pengirimanSample->detail()->orderBy('urut')->get(),
        ]);
    }

    public function update(Request $request, PengirimanSample $pengirimanSample): JsonResponse
    {
        try {
            $this->service->update($pengirimanSample, $request->json()->all());
            return response()->json(['status' => true, 'msg' => 'Berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    public function destroy(PengirimanSample $pengirimanSample): JsonResponse
    {
        try {
            $this->service->hapus($pengirimanSample);
            return response()->json(['status' => true, 'msg' => 'Berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /* ── Toggle tolak satu detail ── */
    public function toggleTolak(PengirimanSampleDetail $detail): JsonResponse
    {
        try {
            $d = $this->service->toggleTolak($detail);
            return response()->json(['status' => true, 'tolak' => $d->tolak]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
      public function riwayat(Request $request)
    {
        if ($request->ajax()) {
            $result = $this->service->getList(
                $request->only(['dari', 'sampai', 'keyword']),
                (int) $request->query('page', 1),
                (int) $request->query('per', 10)
            );
            return response()->json($result);
        }

        $totalShipments = PengirimanSample::count();
        $totalBags = PengirimanSample::sum('total');
        $natShipments = PengirimanSample::where('is_nat', true)->count();
        $rejectedBags = PengirimanSampleDetail::where('tolak', true)->count();

        return view('app.aftap.pengiriman_sample.riwayat', compact(
            'totalShipments', 'totalBags', 'natShipments', 'rejectedBags'
        ));

    }
}