<?php

namespace App\Http\Controllers\Finance\Laporan;

use App\Http\Controllers\IoResourceController;
use App\Services\PosisiKeuanganService;
use Illuminate\Http\Request;

class PosisiKeuanganController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new PosisiKeuanganService();
    }

    // ========================================
    // FOR VIEW
    // ========================================
    public function index()
    {
        return view('app.finance.laporan.posisi_keuangan.index', [
            'tgl_awal'  => request('tgl_awal', date('Y-01-01')),
            'tgl_akhir' => request('tgl_akhir', date('Y-m-d')),
        ]);
    }

    // ========================================
    // JSON UNTUK TABEL (Blade Pertama)
    // ========================================
    public function searchJson(Request $request)
    {
        $data = $this->service->searchTabel($request);

        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }

    // ========================================
    // JSON UNTUK LAPORAN (Blade Chart + PDF)
    // ========================================
    public function searchLaporan(Request $request)
    {
        $data = $this->service->searchLaporan($request);

        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }
}