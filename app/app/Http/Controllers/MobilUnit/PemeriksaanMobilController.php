<?php

namespace App\Http\Controllers\MobilUnit;

use App\Http\Controllers\Controller;
use App\Services\AftapService;
use App\Services\LogDonorService;
use App\Services\PemeriksaanDokterService;
use App\Services\PemeriksaanHBService;
use Illuminate\Http\Request;


class PemeriksaanMobilController extends Controller
{
    protected $logDonorService, $pemeriksaanDokterService, $pemeriksaanHbService, $aftapService;


    public function __construct()
    {
        $this->logDonorService = new LogDonorService();
        $this->pemeriksaanDokterService = new PemeriksaanDokterService();
        $this->pemeriksaanHbService = new PemeriksaanHbService();
        $this->aftapService = new AftapService();

        $list_ecg = ['Normal', 'Tidak Normal'];
        view()->share('list_ecg', $list_ecg);
        $list_puasa = [1 => 'Ya', 0 => 'Tidak'];
        view()->share('list_puasa', $list_puasa);
        $list_status = ['Approved' => 'Terima', 'Rejected' => 'Tolak'];
        view()->share('list_status', $list_status);
        view()->share('list_alasan', $this->pemeriksaanDokterService->list_alasan);
        view()->share('list_jenis_kantong', $this->pemeriksaanDokterService->list_jenis_kantong);
        view()->share('list_tipe_jenis_kantong', $this->pemeriksaanDokterService->list_tipe_jenis_kantong);

        view()->share('golongan_darah_options', $this->pemeriksaanHbService->golongan_darah);
        view()->share('rhesus_options', $this->pemeriksaanHbService->rhesus);
        view()->share('lengan_options', $this->pemeriksaanHbService->lengan);
        view()->share('metode_options', $this->pemeriksaanHbService->metode);

        view()->share('cara_ambil_options', $this->aftapService->cara_ambil);
        view()->share('jenis_donor_options', $this->aftapService->jenis_donor);
        view()->share('reaksi_donor_options', $this->aftapService->reaksi_donor);
    }

    public function index()
    {
        return view('app.mobil_unit.pemeriksaan_mobil.index');
    }

    public function search(Request $request)
    {
        $request->merge([
            'status_not' => 'Pending',
            'date'       => now()->format('Y-m-d') // tambahkan ini
        ]);

        $pemeriksaan_dokters = $this->pemeriksaanDokterService->search($request->all());

        return view('app.mobil_unit.pemeriksaan_mobil._table', compact('pemeriksaan_dokters'));
    }

    public function edit($id)
    {
        $log_donor = $this->logDonorService->find($id);
        $questions = $this->pemeriksaanDokterService->question_kuisioner;
        $pemeriksaan_dokter = $this->pemeriksaanDokterService->find($log_donor->pemeriksaanDokter->id);
        $pemeriksaan_hb = $this->pemeriksaanHbService->find($log_donor->pemeriksaanHb->id);
        $aftap = $this->aftapService->find($log_donor->aftap->id);
        $data_kuisioner = json_decode($pemeriksaan_dokter->data_kuisioner ?? '{}', true) ?: [];

        return view('app.mobil_unit.pemeriksaan_mobil._info', compact('pemeriksaan_dokter', 'id', 'data_kuisioner', 'questions', 'pemeriksaan_hb', 'aftap'));
    }

    public function update(Request $request, $id)
    {
        $log_donor = $this->logDonorService->find($id);

        $kuisioner = [];
        foreach ($this->pemeriksaanDokterService->question_kuisioner as $item) $kuisioner[$item['name']] = $request->input($item['name']) ?? '';
        $request->merge(['data_kuisioner' => json_encode($kuisioner), 'nomor_ruangan' => session('nomor_ruangan')]);

        $pemeriksaan_dokter = $this->pemeriksaanDokterService->update($request->all(), $log_donor->pemeriksaanDokter->id);

        $status = $request->input('status') ?? '';
        if ($status === 'Rejected') {
            $this->logDonorService->update(['step' => 'Rejected'], $pemeriksaan_dokter->log_donor_id);
        } else {
            $log_donor = $this->logDonorService->update(['step' => 'Selesai'], $pemeriksaan_dokter->log_donor_id);

            $pemeriksaan_hb = $this->pemeriksaanHbService->update($request->all(), $log_donor->pemeriksaanHb->id);
            $aftap = $this->aftapService->update($request->all(), $log_donor->aftap->id);
        }

        return $pemeriksaan_dokter;
    }

    public function nomor_ruangan(Request $request)
    {
        session(['nomor_ruangan' => $request->input('nomor_ruangan')]);
    }

    public function log_donor_search(Request $request)
    {
        $request->merge(['date' => date('Y-m-d'), 'step' => 'Registrasi', 'with' => ['donor', 'petugasRegistrasi']]);
        $log_donors = $this->logDonorService->search($request->all());

        return view('app.mobil_unit.pemeriksaan_mobil.log_donor._table', compact('log_donors'));
    }

    public function log_donor_show($id)
    {
        $log_donor = $this->logDonorService->find($id);
        return view('app.mobil_unit.pemeriksaan_mobil.log_donor._info', compact('log_donor'));
    }
}
