<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LogDonor;
use App\Services\MenuService;
use Illuminate\Http\Request;
use App\Services\LogDonorService;

class LauncherController extends Controller
{
    /**
     * Nilai kolom `jenis_donor` pada tabel log_donor yang menandakan donor apheresis.
     * SESUAIKAN nilai ini dengan value asli yang dipakai di database Anda
     * (mis. bisa jadi 'apheresis', 'aferesis', 'trombosit', dll).
     */
    protected const JENIS_DONOR_APHERESIS = 'apheresis';

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
        $logDonorService = new LogDonorService();

        $data = $logDonorService->search([
            'date' => date('Y-m-d'),
            'with' => ['donor', 'pemeriksaanDokter.dokter', 'pemeriksaanHb.dokter'],
        ]);

        return response()->json($data->map(fn ($d) => $this->formatAntrian($d)));
    }

    /**
     * Halaman display antrian khusus apheresis (untuk layar TV di ruang apheresis).
     */
    public function antrian_apheresis()
    {
        return view('app.antrian_apheresis');
    }

    /**
     * Data antrian khusus apheresis (dipoll via AJAX oleh halaman antrian_apheresis).
     */
    public function display_antrian_apheresis_data()
    {
        $logDonorService = new LogDonorService();

        $data = $logDonorService->search([
            'date'        => date('Y-m-d'),
            'jenis_donor' => self::JENIS_DONOR_APHERESIS,
            'with'        => ['donor', 'pemeriksaanDokter.dokter', 'pemeriksaanHb.dokter'],
        ]);

        return response()->json($data->map(fn ($d) => $this->formatAntrian($d)));
    }

    /**
     * Bentuk payload JSON antrian, dipakai bersama oleh antrian umum & apheresis
     * supaya format response-nya konsisten dan tidak duplikat kode.
     */
    protected function formatAntrian(LogDonor $d): array
    {
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
    }
}