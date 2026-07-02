<?php

namespace App\Http\Controllers\Crossmatch;

use App\Http\Controllers\Controller;
use App\Services\RiwayatPasienCrossmatchService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class RiwayatPasienCrossmatchController extends Controller
{
    public function __construct(protected RiwayatPasienCrossmatchService $service)
    {
    }

    /**
     * Halaman daftar pasien + tombol "Lihat Riwayat".
     */
    public function index(Request $request): View
    {
        $keyword    = $request->get('q');
        $pasienList = $this->service->searchPasien($keyword);

        return view('app.crossmatch.riwayat_pasien.index', [
            'pasienList' => $pasienList,
            'keyword'    => $keyword,
        ]);
    }

    /**
     * Endpoint AJAX: mengembalikan timeline riwayat lengkap satu pasien (JSON).
     * Dipanggil dari modal pada index.blade.php.
     */
    public function riwayat(Request $request): JsonResponse
    {
        $noKtp      = $request->get('no_ktp');
        $namaPasien = $request->get('nama_pasien');

        if (empty($noKtp) && empty($namaPasien)) {
            return response()->json([
                'message' => 'Parameter no_ktp atau nama_pasien wajib diisi.',
            ], 422);
        }

        $timeline = $this->service->getRiwayatLengkap($noKtp, $namaPasien);

        return response()->json([
            'pasien' => [
                'no_ktp'      => $noKtp,
                'nama_pasien' => $namaPasien,
            ],
            'total'    => $timeline->count(),
            'timeline' => $timeline,
        ]);
    }
}