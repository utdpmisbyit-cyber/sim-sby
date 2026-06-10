<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use App\Services\AftapService;
use App\Services\DonorService;
use App\Services\LogDonorService;
use App\Services\PemeriksaanDokterService;
use App\Services\PemeriksaanHBService;
use Illuminate\Http\Request;

class PendaftaranMobilController extends Controller
{
    protected $logDonorService;
    public function __construct()
    {
        $this->logDonorService = new LogDonorService();
    }

    public function index()
    {
        $steps = $this->logDonorService->next_step;
        return view('app.mobil_unit.pendaftaran_mobil.index', compact('steps'));
    }

    public function search(Request $request)
    {
        $date = date('Y-m-d');
        $request->merge(['date' => $date, 'with' => ['donor', 'petugasRegistrasi']]);
        $log_donors = $this->logDonorService->search($request->all());
        $steps = $this->logDonorService->next_step;
        $count_steps = [];
        foreach ($steps as $step) $count_steps[$step] = $this->logDonorService->search(['date' => $date, 'step' => $step, 'count' => 1]);

        return view('app.mobil_unit.pendaftaran_mobil._table', compact('log_donors', 'steps', 'count_steps'));
    }

    public function search_donor(Request $request)
    {
        $donorService = new DonorService();
        $donors = $donorService->search(['search' => $request->input('search') ?? '-']);

        return view('app.mobil_unit.pendaftaran_mobil._table_donor', compact('donors'));
    }

    public function store(Request $request)
    {
        $donor_id = $request->input('donor_id');
        $check = $this->logDonorService->search(['date' => date('Y-m-d'), 'donor_id' => $donor_id, 'first' => 1]);
        if (!empty($check)) return ['error' => 'Hari ini sudah terdaftar!'];
        $log_donor = $this->logDonorService->store([
            'kode' => $this->logDonorService->autoKode(),
            'cabang_id' => $request->input('cabang_id'),
            'donor_id' => $donor_id,
            'petugas_registrasi_id' => auth()->user->petugas->id ?? 1,
            'step' => 'Dokter',
        ]);

        if (empty($log_donor->pemeriksaanDokter)) {
            $pemeriksaanDokterService = new PemeriksaanDokterService();
            $pemeriksaanDokterService->store([
                'kode' => $pemeriksaanDokterService->generateKode(),
                'log_donor_id' => $log_donor->id,
                'dokter_id' => auth()->user()->petugas->id ?? 1,
                'donor_id' => $log_donor->donor_id,
                'status' => 'Pending',
            ]);
        }

        if (empty($log_donor->pemeriksaanHb)) {
            $pemeriksaanHbService = new PemeriksaanHbService();
            $pemeriksaanHbService->store([
                'kode' => $pemeriksaanHbService->generateKode(),
                'log_donor_id' => $log_donor->id,
                'dokter_id' => auth()->user()->petugas->id ?? 1,
                'donor_id' => $log_donor->donor_id,
                'status' => 'Pending',
            ]);
        }

        if (empty($log_donor->aftap)) {
            $aftapService = new AftapService();
            $aftapService->store([
                'kode' => $aftapService->generateKode(),
                'log_donor_id' => $log_donor->id,
                'dokter_id' => auth()->user()->petugas->id ?? 1,
                'donor_id' => $log_donor->donor_id,
                'status' => 'Pending',
            ]);
        }

        return $log_donor;
    }

    public function destroy($id)
    {
        return $this->logDonorService->delete($id);
    }
}
