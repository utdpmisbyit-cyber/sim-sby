<?php

namespace App\Http\Controllers\Apheresis;

use App\Http\Controllers\Controller;
use App\Services\DonorService;
use App\Services\JenisKantongService;
use App\Services\LogDonorService;
use App\Services\PemeriksaanDokterService;
use App\Services\PemeriksaanHBService;
use App\Services\PetugasService;
use App\Services\TipeKantongService;
use Illuminate\Http\Request;


class PemeriksaanKesehatanController extends Controller
{
    protected $logDonorService, $pemeriksaanDokterService;

    public function __construct()
    {
        $this->logDonorService = new LogDonorService();
        $this->pemeriksaanDokterService = new PemeriksaanDokterService();

        $list_ecg = ['Normal', 'Tidak Normal'];
        view()->share('list_ecg', $list_ecg);
        $list_puasa = [1 => 'Ya', 0 => 'Tidak'];
        view()->share('list_puasa', $list_puasa);
        $list_status = ['Approved' => 'Terima', 'Rejected' => 'Tolak'];
        view()->share('list_status', $list_status);
        view()->share('list_alasan', $this->pemeriksaanDokterService->list_alasan);

        $petugasService = new PetugasService();
        view()->share('dokter_options', $petugasService->search(['nama_jabatan' => 'Dokter']));
        $jenisKantongService = new JenisKantongService();
        view()->share('jenis_kantong_options', $jenisKantongService->dropdown());
        $tipeKantongService = new TipeKantongService();
        view()->share('tipe_kantong_options', $tipeKantongService->search());
    }

    public function index()
    {
        return view('app.apheresis.pemeriksaan_kesehatan.index');
    }
  
   // PemeriksaanKesehatanController.php

public function panggil(Request $request, $id)
{
    $petugas   = \App\Models\Petugas::where('user_id', auth()->id())->first();
    $log_donor = $this->logDonorService->find($id);

    // Wajib ada nomor_ruangan
    if (empty($log_donor->nomor_ruangan)) {
        return response()->json([
            'message' => 'Ruangan belum dipilih'
        ], 422);
    }

    $this->logDonorService->update(['step' => 'Kesehatan','status' => 'Terpanggil'], $id);

    if (empty($log_donor->pemeriksaanDokter)) {

    $pemeriksaan = $this->pemeriksaanDokterService->store([
        'kode'           => $this->pemeriksaanDokterService->generateKode(),
        'log_donor_id'   => $log_donor->id,
        'donor_id'       => $log_donor->donor_id,
        'status'         => 'Terpanggil',
        'dokter_id'      => $petugas?->id,
        'nomor_ruangan'  => $log_donor->nomor_ruangan,
    ]);

} else {

    // update kalau sudah ada
    $this->pemeriksaanDokterService->update([
        'dokter_id'     => $petugas?->id,
        'nomor_ruangan' => $log_donor->nomor_ruangan,
    ], $log_donor->pemeriksaanDokter->id);
}

    return response()->json([
        'success'       => true,
        'dokter_nama'   => $petugas?->nama,
        'nomor_ruangan' => $log_donor->nomor_ruangan,
    ]);
}

// Method untuk assign ruangan ke donor saat registrasi/pilih
public function assign_ruangan(Request $request, $id)
{
    $nomor_ruangan = $request->input('nomor_ruangan');
    $this->logDonorService->update(['nomor_ruangan' => $nomor_ruangan], $id);
    return response()->json(['success' => true]);
}
    public function search(Request $request)
    {
        $request->merge(['status_not' => 'Pending', 'date' =>date('Y-m-d'),
             'with' => [
            'donor',
            'dokter',
            'tipeKantong' 
        ]
        ]);
        $pemeriksaan_dokters = $this->pemeriksaanDokterService->search($request->all());
        return view('app.apheresis.pemeriksaan_kesehatan._table', compact('pemeriksaan_dokters'));
    }

    public function edit($id)
    {
        $questions = $this->pemeriksaanDokterService->question_kuisioner;
        $pemeriksaan_dokter = $this->pemeriksaanDokterService->find($id);
        $pemeriksaan_dokter->load('tipeKantong');
        $data_kuisioner = json_decode($pemeriksaan_dokter->data_kuisioner ?? '{}', true) ?: [];

        return view('app.apheresis.pemeriksaan_kesehatan._info', compact('pemeriksaan_dokter', 'id', 'data_kuisioner', 'questions'));
    }

    public function update(Request $request, $id)
    {
        if ($request->has('dokter_id')) {
            return $this->pemeriksaanDokterService->update(['dokter_id' => $request->input('dokter_id')], $id);
        }

        $kuisioner = [];
        foreach ($this->pemeriksaanDokterService->question_kuisioner as $item) $kuisioner[$item['name']] = $request->input($item['name']) ?? '';
        $request->merge(['data_kuisioner' => json_encode($kuisioner), 'nomor_ruangan' => session('nomor_ruangan')]);

        $pemeriksaan_dokter = $this->pemeriksaanDokterService->update($request->all(), $id);

        $status = $request->input('status') ?? '';
        if ($status === 'Rejected') {
            $this->logDonorService->update(['step' => 'Rejected'], $pemeriksaan_dokter->log_donor_id);

            if ($request->input('alasan') === 'Cekal') {
                $donorService = new DonorService();
                $donorService->update([
                    'cekal' => 'Ya',
                    'tanggal_cekal' => date('Y-m-d')
                ], $pemeriksaan_dokter->donor_id);
            }

        } else {
            $log_donor = $this->logDonorService->update(['step' => 'Kesehatan'], $pemeriksaan_dokter->log_donor_id);

            if (empty($log_donor->pemeriksaanHb)) {
                $pemeriksaanHbService = new PemeriksaanHbService();
                $pemeriksaanHbService->store([
                    'kode' => $pemeriksaanHbService->generateKode(),
                    'log_donor_id' => $log_donor->id,
                    'donor_id' => $log_donor->donor_id,
                    'status' => 'Pending',
                ]);
            }
        }

        return $pemeriksaan_dokter;
    }

    public function nomor_ruangan(Request $request)
    {
        session(['nomor_ruangan' => $request->input('nomor_ruangan')]);
    }

    public function log_donor_search(Request $request)
    {
        $request->merge(['date' => date('Y-m-d'),
         'step' => 'Registrasi', 
         'with' => ['donor', 'petugasRegistrasi', 'pemeriksaanDokter.dokter']]);
        $log_donors = $this->logDonorService->search($request->all());

        return view('app.apheresis.pemeriksaan_kesehatan.log_donor._table', compact('log_donors'));
    }

    public function log_donor_show($id)
{
    $log_donor = $this->logDonorService->find($id);

    $log_donor->load([
        // data sekarang
        'pemeriksaanDokter',
        'pemeriksaanDokter.tipeKantong',

        // riwayat donor
        'donor',
        'donor.logDonorAftap',
        'donor.logDonorAftap.pemeriksaanDokter',
        'donor.logDonorAftap.pemeriksaanDokter.tipeKantong',
    ]);

    if (empty($log_donor->pemeriksaanDokter)) {

        $pemeriksaan = $this->pemeriksaanDokterService->store([
            'kode'         => $this->pemeriksaanDokterService->generateKode(),
            'log_donor_id' => $log_donor->id,
            'donor_id'     => $log_donor->donor_id,
            'status'       => 'Pending',
        ]);

        $log_donor->pemeriksaanDokter = $pemeriksaan;
    }

    $today = date('Y-m-d');

    $antrian = $this->logDonorService->search([
        'date' => $today
    ]);

    $total = $antrian->count();

    $dipanggil = $antrian->where('step', 'Kesehatan')->count();

    $proses = $antrian->whereIn('step', ['HB','Aftap'])->count();

    $selesai = $antrian->whereIn('step', ['Approved','Rejected','Selesai'])->count();

    return view(
        'app.apheresis.pemeriksaan_kesehatan.log_donor._info',
        compact(
            'log_donor',
            'total',
            'dipanggil',
            'proses',
            'selesai'
        )
    );
}
}
