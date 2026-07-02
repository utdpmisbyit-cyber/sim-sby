<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\MenuService;
use Illuminate\Http\Request;
use App\Services\LogDonorService;

class LauncherController extends Controller
{
    public function index()
    {
        $menuService = new MenuService();
        $modules = $menuService->modules;
        return view('app.launcher', compact('modules'));
    }

    public function antrian_dokter()
    {
        return view('app.antrian_dokter');
    }
   
    public function display_antrian_data()
    {
        $logDonorService = new \App\Services\LogDonorService();

        $data = $logDonorService->search([
            'date' => date('Y-m-d'),
            'with' => ['donor', 'pemeriksaanDokter.dokter', 'pemeriksaanHb.dokter'],
        ]);

        return response()->json($data->map(function ($d) {
            return [
                'id'            => $d->id,
                'kode'          => $d->kode,
                'step'          => $d->step,
                'nomor_ruangan' => $d->nomor_ruangan,
                'donor' => [
                    'nama' => optional($d->donor)->nama,
                ],
                'pemeriksaan_dokter' => [
                    'nomor_ruangan' => optional($d->pemeriksaanDokter)->nomor_ruangan,
                    'dokter' => [
                        'nama' => optional(optional($d->pemeriksaanDokter)->dokter)->nama,
                    ],
                ],
                'pemeriksaan_hb' => [
                    'dokter' => [
                        'nama' => optional(optional($d->pemeriksaanHb)->dokter)->nama,
                    ],
                ],
            ];
        }));
    }

   
     
}
