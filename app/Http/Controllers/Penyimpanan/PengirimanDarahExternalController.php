<?php

namespace App\Http\Controllers\Penyimpanan;

use App\Http\Controllers\Controller;
use App\Services\PengirimanDarahExternalService;
use App\Models\PengirimanDarahExternal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class PengirimanDarahExternalController extends Controller
{
    public function __construct(
        protected PengirimanDarahExternalService $service
       
    ) {}

    public function index()
    {
        return view('app.penyimpanan.pengiriman_darah_external.index');
    }

    public function getData(Request $request): JsonResponse
    {
        $query = $this->service->getData($request->only([
            'no_permintaan', 'tanggal_dari', 'tanggal_sampai', 'status',
        ]));

        $data = $query->get()->map(fn($r) => [
            'id'               => $r->id,
            'nomor_pengiriman' => $r->nomor_pengiriman,
            'no_permintaan'    => $r->no_permintaan,
            'tanggal_kirim'    => $r->tanggal_kirim
                                    ? \Carbon\Carbon::parse($r->tanggal_kirim)->format('d-m-Y H:i')
                                    : '-',
            'institusi_tujuan' => $r->institusi_tujuan,
            'jenis_biaya'      => $r->jenis_biaya,
            'dropping'         => $r->dropping,
            'petugas'          => $r->petugas,
            'suhu_kirim'       => $r->suhu_kirim,
            'status'           => $r->status,
        ]);

        return response()->json(['data' => $data]);
    }

    public function getPermintaan(Request $request): JsonResponse
    {
        $request->validate(['no_permintaan' => 'required|string']);

        $data = $this->service->getPermintaanByNomor($request->no_permintaan);

        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Nomor permintaan tidak ditemukan.'], 404);
        }

        if ($data['status'] === 'SUDAH_DIPENUHI') {
            return response()->json(['success' => false, 'message' => 'Permintaan ini sudah dipenuhi seluruhnya.'], 422);
        }

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function cariStok(Request $request): JsonResponse
    {
        $request->validate([
            'jenis_darah' => 'required|string',
            'gol_darah'   => 'required|string',
            'rhesus'          => 'required|string',
        ]);

        try {
            $stok = $this->service->cariStokTersedia(
                $request->jenis_darah,
                $request->gol_darah,
                $request->rhesus
            );

            return response()->json(['success' => true, 'data' => $stok, 'total' => count($stok)]);

        } catch (\Exception $e) {
            Log::error('cariStok controller error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memuat stok: ' . $e->getMessage()], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $pengiriman = PengirimanDarahExternal::with(['details', 'permintaan.details'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $pengiriman]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'permintaan_id'         => 'required|exists:permintaan_darah_external,id',
            'no_permintaan'         => 'required|string',
            'tanggal_kirim'         => 'required|date',
            'petugas'               => 'required|string',
            'petugas_kode'          => 'required|string',
            'jenis_biaya'           => 'required|in:Dropping,Konfalesen,BPJS,ASURASI',
            'dropping'              => 'nullable|in:AMBIL_SENDIRI,DIANTAR,KURIR',
            'suhu_kirim'            => 'nullable|numeric',
            'details'               => 'required|array|min:1',
            
            'details.*.no_stock'    => 'nullable|string',
            'details.*.jenis_darah' => 'nullable|string',
            'details.*.gol_darah'   => 'nullable|string',
            'details.*.rhesus'      => 'nullable|in:Positif,Negatif',
            'details.*.jumlah'      => 'nullable|integer|min:1',
        ]);

        try {
            // Semua mutasi stok (keluar + transaksi) sudah ada di service->store()
            $pengiriman = $this->service->store($request->all());

            return response()->json([
                'success'          => true,
                'message'          => 'Pengiriman berhasil disimpan.',
                'nomor_pengiriman' => $pengiriman->nomor_pengiriman,
                'data'             => $pengiriman,
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'tanggal_kirim' => 'required|date',
            'petugas'       => 'required|string',
            'petugas_kode'  => 'required|string',
            'jenis_biaya'   => 'required|in:Dropping,Konfalesen,BPJS,ASURASI',
            'details'       => 'required|array|min:1',
        ]);

        try {
            $pengiriman = $this->service->update($id, $request->all());
            return response()->json(['success' => true, 'message' => 'Pengiriman berhasil diperbarui.', 'data' => $pengiriman]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            // ← FIX: dulu $pengiriman undefined setelah ->delete()
            // Sekarang semua rollback stok sudah ada di service->destroy()
            $this->service->destroy($id);

            return response()->json([
                'success' => true,
                'message' => 'Pengiriman berhasil dihapus dan stok dikembalikan.',
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus: ' . $e->getMessage()], 500);
        }
    }
    
    public function nextNomor(): JsonResponse
    {
        return response()->json(['success' => true, 'nomor' => $this->service->generateNomorPengiriman()]);
    }
}