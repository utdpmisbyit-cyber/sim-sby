<?php

namespace App\Http\Controllers\Penyimpanan;

use App\Http\Controllers\Controller;
use App\Services\PengirimanBankDarahInternalService;
use App\Services\StokDarahService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Exception;

class PengirimanBankDarahInternalController extends Controller
{
    public function __construct(
        protected StokDarahService $stokService,
        protected PengirimanBankDarahInternalService $service
    ) {}

    /**
     * Page
     */
    public function index(): View
    {
        return view(
            'app.penyimpanan.pengiriman_bank_darah_internal.index'
        );
    }

    /**
     * Datatable
     */
    public function getData(): JsonResponse
    {
        try {
            $data = $this->service->getData();

            return response()->json([
                'success' => true,
                'data'    => $data
            ]);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dropdown permintaan
     */
    public function update(  Request $request, int $id ): JsonResponse 
    {

        try {

            $data =$this->service->update(
                    $id,
                    $request->all()
                );

            return response()->json([
                'success' => true,
                'message' =>
                    'Data berhasil diupdate',
                'data' => $data
            ]);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' =>
                    $e->getMessage()
            ], 500);
        }
    }
    public function getPermintaan(): JsonResponse
    {
        try {

            $data = $this->service->getPermintaan();
            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function show(int $id): JsonResponse
{
    try {

        $data = $this->service->findById($id);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);

    } catch (Exception $e) {

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
    /**
     * Show detail permintaan
     */
    public function showPermintaan(int $id): JsonResponse
    {
        try {
            $data = $this->service->getPermintaanById($id);
            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

     public function cariStok(Request $request): JsonResponse
    {
        try {
            $data = $this->service->cariStokByNoStok($request->no_stok);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'permintaan_id' => 'required|exists:permintaan_darah_penyimpanan,id',
            'keterangan' => 'nullable|string'
        ]);

        try {

            $data = $this->service->store($request->all());
            
            $this->stokService->keluar(
                noStok      : $request->no_stok,
                jumlah      : $request->jumlah ?? 1,
                noReferensi : $pengiriman->no_pengiriman,
                sumber      : 'pengiriman_internal',
                referensiId : $pengiriman->id
            );
            return response()->json([
                'success' => true,
                'message' =>
                    'Pengiriman berhasil diproses',
                'data' => $data
            ]);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' =>
                    $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete / rollback
     */
    public function destroy( int $id): JsonResponse
    {
        try {

            $this->service->destroy($id);
            // Rollback: kembalikan stok
            $this->stokService->kembali(
                noStok      : $pengiriman->no_stok,
                jumlah      : $pengiriman->jumlah ?? 1,
                noReferensi : $pengiriman->no_pengiriman,
                sumber      : 'pengiriman_internal',
                referensiId : $pengiriman->id,
                keterangan  : 'Rollback: pengiriman dibatalkan'
            );

            return response()->json([
                'success' => true, 
                'message' =>
                'Data berhasil dihapus'
            ]);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' =>
                    $e->getMessage()
            ], 500);
        }
    }
}