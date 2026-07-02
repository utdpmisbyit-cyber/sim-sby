<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Services\RiwayatBarcodeService;
use Illuminate\Http\Request;

class RiwayatBarcodeController extends Controller
{
    protected $riwayatBarcodeService;

    public function __construct()
    {
        $this->riwayatBarcodeService = new RiwayatBarcodeService();
    }

    public function index()
    {
        $merk_options   = $this->riwayatBarcodeService->merkOptions();
        $jenis_options  = $this->riwayatBarcodeService->jenisOptions();
        $type_options   = $this->riwayatBarcodeService->typeOptions();
        $status_options = $this->riwayatBarcodeService->distinctStatus();
        $jenis_type_map = $this->riwayatBarcodeService->jenisTypeMap();

        return view('app.gudang.riwayat_barcode.index', compact(
            'merk_options',
            'jenis_options',
            'type_options',
            'status_options',
            'jenis_type_map'
        ));
    }

    /**
     * Tabel riwayat (dipanggil via AJAX, mengembalikan partial blade).
     */
    public function data(Request $request)
    {
        $riwayats = $this->riwayatBarcodeService->search($request->all());

        return view('app.gudang.riwayat_barcode._table', compact('riwayats'));
    }

    /**
     * Rekap jumlah per merk & jenis (dipanggil via AJAX, mengembalikan JSON).
     */
    public function summary(Request $request)
    {
        $params = $request->all();

        return response()->json([
            'total'     => $this->riwayatBarcodeService->totalGenerated($params),
            'per_merk'  => $this->riwayatBarcodeService->summaryByMerk($params),
            'per_jenis' => $this->riwayatBarcodeService->summaryByJenis($params),
            'per_type'  => $this->riwayatBarcodeService->summaryByType($params),
        ]);
    }
}