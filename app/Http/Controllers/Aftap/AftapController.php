<?php

namespace App\Http\Controllers\Aftap;

use App\Http\Controllers\Controller;
use App\Services\AftapService;
use App\Services\LogDonorService;
use App\Services\PenyimpananKantongService;
use App\Services\PetugasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AftapController extends Controller
{
    protected $logDonorService, $aftapService;

    public function __construct()
    {
        $this->logDonorService = new LogDonorService();
        $this->aftapService    = new AftapService();

        view()->share('cara_ambil_options',   $this->aftapService->cara_ambil);
        view()->share('jenis_donor_options',  $this->aftapService->jenis_donor);
        view()->share('reaksi_donor_options', $this->aftapService->reaksi_donor);

        $petugasService = new PetugasService();
        view()->share('dokter_options', $petugasService->search(['nama_jabatan' => 'Dokter']));
    }

    public function index()
    {
        return view('app.aftap.index');
    }

    public function search(Request $request)
    {
        $request->merge(['status_not' => 'Pending', 'date' => date('Y-m-d')]);
        $aftaps = $this->aftapService->search($request->all());
        return view('app.aftap._table', compact('aftaps'));
    }

        public function edit($id)
    {
        
        $aftap = \App\Models\Aftap::with([
            'logDonor.pemeriksaanDokter.tipeKantong.jenisKantong',
            'logDonor.pemeriksaanHb',
            'logDonor.donor',
        ])->findOrFail($id);

        return view('app.aftap._info', compact('aftap', 'id'));
    }

     public function update(Request $request, $id)
    {
       
        if ($request->has('dokter_id')) {

            return $this->aftapService->update([
                'dokter_id' => $request->input('dokter_id')
            ], $id);
        }

      
        $logDonor = \App\Models\LogDonor::with([
            'donor',
            'pemeriksaanHb',
            'pemeriksaanDokter'
        ])->findOrFail($request->log_donor_id);

      
        $asalDarahId = optional($logDonor->donor)->asal_darah_id;

       
        $lengan = optional($logDonor->pemeriksaanHb)->lengan ?? 'kiri';

        
        $cc_raw = null;

        if ($request->filled('cc_manual')) {

            $cc_raw = $request->cc_manual;

        } elseif (
            $request->filled('stop_pada') &&
            strtoupper(trim($request->stop_pada)) !== 'STOP'
        ) {

            $cc_raw = $request->stop_pada;
        }

        $cc     = 0;
        $status = 'Approved';

       
        if (!is_null($cc_raw)) {

            $cc = (int) $cc_raw;

            if ($cc < 250) {

                $status = 'Gagal';

            } else {

                $status = 'Approved';
            }
        }

        if (
            $request->filled('stop_pada') &&
            strtoupper(trim($request->stop_pada)) === 'STOP'
        ) {

            $status = 'Gagal';
        }

       
        $kantongPenuh = $cc >= 250 ? 1 : 0;

       
        $request->merge([
            'asal_darah_id' => $asalDarahId,
            'lengan'         => $lengan,
            'cc_ambil'       => $cc,
            'stop_pada'      => $cc > 0
                                    ? $cc
                                    : $request->stop_pada,
            'kantong_penuh'  => $kantongPenuh,
            
            'status'         => $status,
        ]);

        if ($request->filled('tipe_kantong_id')) {

            \App\Models\PemeriksaanDokter::where(
                'log_donor_id',
                $request->log_donor_id
            )->update([
                'tipe_kantong_id' => $request->tipe_kantong_id
            ]);
        }

        
        if ($request->filled('asal_darah')) {

            \App\Models\PemeriksaanDokter::where(
                'log_donor_id',
                $request->log_donor_id
            )->update([
                'asal_darah' => $request->asal_darah
            ]);
        }

        
        $aftap = $this->aftapService->update(
            $request->all(),
            $id
        );

        
        $this->logDonorService->update([
            'step' => 'Aftap'
        ], $aftap->log_donor_id);

        return response()->json([
            'success' => true,
            'message' => 'Data aftap berhasil disimpan',
            'data'    => $aftap
        ]);
    }

        public function log_donor_search(Request $request)
    {
        $log_donors = \App\Models\LogDonor::with([
                'donor',
                'petugasRegistrasi',
                'aftap',
                'aftap.petugasPanggil',
            ])
            ->whereDate('created_at', date('Y-m-d'))  // ← hapus prefix tabel
            ->whereHas('aftap', function ($q) {
                $q->whereIn('status', ['Pending', 'Ongoing']);
            })
            ->orderBy('created_at', 'asc')             // ← hapus prefix tabel
            ->get();

        return view('app.aftap.log_donor._table', compact('log_donors'));
    }

    public function log_donor_show($id)
    {
    $log_donor = \App\Models\LogDonor::with([
        'donor',
        'donor.logDonorAftap',
        'donor.logDonorAftap.pemeriksaanDokter',
        'donor.logDonorAftap.pemeriksaanDokter.tipeKantong',

        'aftap',
        'pemeriksaanHb',
        'pemeriksaanDokter',
        'pemeriksaanDokter.tipeKantong',
        'pemeriksaanDokter.tipeKantong.jenisKantong',
    ])->findOrFail($id);

        return view('app.aftap.log_donor._info', compact('log_donor'));
    }

        public function panggil(Request $request, $id)
    {
        $request->validate(['bed' => 'required|integer|min:1|max:18']);

        $log_donor = $this->logDonorService->find($id);
        $aftap     = $log_donor->aftap;

        $lengan = strtolower(trim(
            $request->input('lengan')
            ?? optional($log_donor->pemeriksaanHb)->lengan
            ?? 'kiri'
        ));

        $prefix = ($lengan === 'kanan') ? 'A' : 'B';

        // ambil 4 digit belakang kode log donor
        $angkaAsli = (int) substr($log_donor->kode, -4);
        $angkaAntrian = str_pad($angkaAsli, 3, '0', STR_PAD_LEFT);
        $nomor_antrian = $prefix . $angkaAntrian;

        $noUrut = $angkaAsli;
        $petugas    = auth()->user()->petugas ?? null;
        $petugas_id = $petugas?->id;

        $this->aftapService->update([
            'status'             => 'Ongoing',
            'bed'                => $request->bed,
            'nomor_antrian'      => $nomor_antrian,
            'lengan'             => $lengan,  
            'petugas_panggil_id' => $petugas_id,
            'dokter_id'          => $petugas_id,
            'called_at'          => now(),
        ], $aftap->id);

        $this->logDonorService->update(['step' => 'Aftap'], $log_donor->id);

        return response()->json([
            'success'       => true,
            'aftap_id'      => $aftap->id,
            'bed'           => $request->bed,
            'nomor_antrian' => $nomor_antrian, 
            'no_urut'       => $noUrut,    
            'lengan'        => $lengan,   
            'nama'          => $log_donor->donor->nama,
            'kode'          => $log_donor->kode,
            'petugas'       => $petugas?->nama ?? '',
            'called_at'     => now()->format('H:i:s'),
        ]);
    }

    public function scan_kantong(Request $request)
    {
        $data = DB::table('stok_kantong_keluar as sk')
            ->leftJoin('aturan_satelit as a', 'a.typektg', '=', 'sk.tipe')
            ->where('sk.no_kantong', $request->no_kantong)
            ->select(
                'sk.no_kantong',
                'sk.ukuran',
                'sk.tipe',
                'a.satelit'
            )
            ->first();

        if (!$data) {

            return response()->json([
                'success' => false,
                'message' => 'No kantong tidak ditemukan'
            ]);
        }

        $is_stop = strtoupper(trim($data->ukuran)) == 'STOP';

        return response()->json([
            'success' => true,
            'data' => [
                'cc'        => $data->ukuran,
                'satelit'   => $data->satelit ?? 0,
                'is_stop'   => $is_stop,
            ]
        ]);
    }
        public function display_antrian()
        {
            return view('app.antrian_aftap');
        }

    public function display_antrian_data()
    {
        try {

            $raw = \App\Models\Aftap::whereDate('created_at', date('Y-m-d'))
                ->where('status', 'Ongoing')
                ->orderBy('updated_at', 'desc')
                ->take(20)
                ->get();

            $antrian = $raw->map(function($a) {

                $logDonor = \App\Models\LogDonor::with([
                    'donor',
                    'pemeriksaanHb'
                ])->find($a->log_donor_id);

                $petugas = $a->petugas_panggil_id
                    ? \App\Models\Petugas::find($a->petugas_panggil_id)
                    : null;

                $lengan = strtolower(
                    trim(
                        $a->lengan
                        ?? optional($logDonor->pemeriksaanHb)->lengan
                        ?? 'kiri'
                    )
                );

                $prefix = $lengan === 'kanan' ? 'A' : 'B';

                $angkaAsli = (int) substr($logDonor->kode ?? '0000', -4);

                $nomorAntrian = $prefix . str_pad($angkaAsli, 3, '0', STR_PAD_LEFT);

                return [
                    'aftap_id'      => $a->id,
                    'nomor_antrian' => $nomorAntrian,
                    'lengan'        => $lengan,
                    'nama'          => $logDonor->donor->nama ?? '-',
                    'kode'          => $logDonor->kode ?? '-',
                    'bed'           => $a->bed ?? '-',
                    'petugas'       => $petugas->nama ?? '-',
                    'called_at'     => \Carbon\Carbon::parse($a->updated_at)->format('H:i:s'),
                ];
            });

            return response()->json($antrian);

        } catch (\Throwable $e) {

            return response()->json([
                'error'   => true,
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => basename($e->getFile()),
            ], 500);
        }
    }
}