<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use App\Services\AftapService;
use App\Services\LogDonorService;
use App\Services\PemeriksaanHbService;
use App\Services\PetugasService;
use Illuminate\Http\Request;


class PemeriksaanHbController extends Controller
{
    protected $logDonorService, $pemeriksaanHbService;


    public function __construct()
    {
        $this->logDonorService = new LogDonorService();
        $this->pemeriksaanHbService = new PemeriksaanHbService();

        view()->share('golongan_darah_options', $this->pemeriksaanHbService->golongan_darah);
        view()->share('rhesus_options', $this->pemeriksaanHbService->rhesus);
        view()->share('lengan_options', $this->pemeriksaanHbService->lengan);
        view()->share('metode_options', $this->pemeriksaanHbService->metode);
        $list_status = ['Approved' => 'Terima', 'Rejected' => 'Tolak'];
        view()->share('list_status', $list_status);
        view()->share('list_alasan', ['HB Rendah', 'HB Tinggi']);

        $petugasService = new PetugasService();
        view()->share('dokter_options', $petugasService->search(['nama_jabatan' => 'Dokter']));
    }

    public function index()
    {
        return view('app.unit.pemeriksaan_hb.index');
    }
    public function panggil(Request $request, $id)
    {
        
        $log_donor = $this->logDonorService->update([
            'status' => 'Terpanggil',
            'step' => 'HB',
            
        ], $id);

         return response()->json([
            'success' => true,
            'nomor_ruangan' => $log_donor->nomor_ruangan ?? 1
        ]);
    }

    public function assign_ruangan(Request $request, $id)
    {
        $log_donor = $this->logDonorService->update([
            'nomor_ruangan' => $request->input('nomor_ruangan')
        ], $id);
        return response()->json(['success' => true]);
    }
    public function search(Request $request)
    {
        $request->merge(['status_not' => 'Pending', 'date' => date('Y-m-d')]);
        $pemeriksaan_hbs = $this->pemeriksaanHbService->search(
            array_merge($request->all(), [
                'with' => ['donor', 'dokter']
            ]),
        );
        return view('app.unit.pemeriksaan_hb._table', compact('pemeriksaan_hbs'));
    }

    public function edit($id)
    {
        $pemeriksaan_hb = $this->pemeriksaanHbService->find($id);
        return view('app.unit.pemeriksaan_hb._info', compact('pemeriksaan_hb', 'id'));
    }

    public function update(Request $request, $id)
    {
        $petugas = \App\Models\Petugas::where('user_id', auth()->id())->first();

        if ($request->has('dokter_id')) {
            return $this->pemeriksaanHbService->update([
                'dokter_id' => $request->input('dokter_id')], $id);
        }

        $data = $request->all();

        if ($petugas) {
            // Selalu isi dokter_id dengan petugas yang sedang login saat
            // menyimpan hasil pemeriksaan, supaya kolom ini tidak kosong.
            $data['dokter_id'] = $petugas->id;
        } else {
            $existing = $this->pemeriksaanHbService->find($id);
            if (empty($existing?->dokter_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda belum terhubung ke data Petugas, sehingga Dokter tidak dapat diisi. Hubungi admin untuk menautkan akun Anda.'
                ], 422);
            }
            // Sudah ada dokter_id sebelumnya, jangan ditimpa jadi kosong.
        }

        $pemeriksaan_hb = $this->pemeriksaanHbService->update($data, $id);
        $status = $request->input('status') ?? '';
        if ($status === 'Rejected') {
            $this->logDonorService->update(['step' => 'Rejected'], $pemeriksaan_hb->log_donor_id);
        } else {
            $log_donor = $this->logDonorService->update([
                'step' => 'Aftap'], $pemeriksaan_hb->log_donor_id);

            if (empty($log_donor->aftap)) {
                $aftapService = new AftapService();
                $aftapService->store([
                    'kode' => $aftapService->generateKode(),
                    'log_donor_id' => $log_donor->id,
                    'donor_id'     => $log_donor->donor_id,
                    'dokter_id'    => $petugas?->id,
                    'petugas_aftap_id'  => $petugas?->id,
                    'status'       => 'Pending',
                ]);
            }
        }
        return $pemeriksaan_hb;
    }

    public function log_donor_search(Request $request)
    {
        $request->merge(['date' => date('Y-m-d'),
         'step_in' => ['Kesehatan', 'HB'], 
         'with' => ['donor', 'petugasRegistrasi']]);
        $log_donors = $this->logDonorService->search(
             array_merge($request->all(), [
                'with' => ['donor', 'pemeriksaanHb.dokter', 'pemeriksaanDokter']
            ])
        );

        return view('app.unit.pemeriksaan_hb.log_donor._table', compact('log_donors'));
    }

    public function log_donor_show($id)
    {
        try {
            $petugas   = \App\Models\Petugas::where('user_id', auth()->id())->first();

            $log_donor = \App\Models\LogDonor::with([
                'donor.logDonorAftap',
                'pemeriksaanHb.dokter',
                'pemeriksaanDokter',
                'pemeriksaanDokter.tipeKantong',
                'pemeriksaanDokter.tipeKantong.jenisKantong',

                'donor',
                'donor.logDonorAftap',
                'donor.logDonorAftap.pemeriksaanDokter',
                'donor.logDonorAftap.pemeriksaanDokter.tipeKantong',
                'donor.logDonorAftap.pemeriksaanDokter.tipeKantong.jenisKantong',
            ])->find($id);

            if (!$log_donor) {
                return response()->json(['error' => 'Data tidak ditemukan'], 404);
            }

            if (!$log_donor->pemeriksaanHb) {
                $log_donor->pemeriksaanHb()->create([
                    'kode'         => $this->pemeriksaanHbService->generateKode(),
                    'log_donor_id' => $log_donor->id,
                    'donor_id'     => $log_donor->donor_id,
                    'dokter_id'    => $petugas?->id,
                    'status'       => 'Pending'
                ]);

                  $log_donor->load([
                        'pemeriksaanHb',
                        'donor.logDonorAftap',
                        'pemeriksaanDokter.tipeKantong.jenisKantong',
                    ]);
            }

            return view('app.unit.pemeriksaan_hb.log_donor._info', compact('log_donor'));

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function kirimWaAntrian($id)
{
    $log = \App\Models\LogDonor::with('donor')->find($id);

    if (!$log || !$log->donor) {
        return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
    }

    $no_hp = preg_replace('/^0/', '62', $log->donor->no_telp);

    $noAntrian = (int) substr($log->kode, -3);
    $kodeAftap = 'AF' . str_pad($noAntrian, 4, '0', STR_PAD_LEFT); 

    $pesan = "Nomor antrian *{$kodeAftap}*\n" .
             "Nama: {$log->donor->nama}\n" .
             "Silakan menuju meja pemeriksaan HB";

    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'Authorization' => 'TOKEN_KAMU'
    ])->post('https://api.fonnte.com/send', [
        'target'  => $no_hp,
        'message' => $pesan,
    ]);

    return response()->json([
        'success'  => true,
        'response' => $response->json()
    ]);
}
}