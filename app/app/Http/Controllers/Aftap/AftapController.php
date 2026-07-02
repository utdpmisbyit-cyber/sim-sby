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
            'logDonor.donor.asalDarah',
            'dokter',
        ])->findOrFail($id);

        return view('app.aftap._info', compact('aftap', 'id'));
    }

    public function update(Request $request, $id)
    {
         if ($request->has('petugas_aftap_id')) {

            return $this->aftapService->update([
                'petugas_aftap_id' => $request->input('petugas_aftap_id')
            ], $id);
        }

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
            ->whereDate('created_at', date('Y-m-d'))
            ->whereHas('aftap', function ($q) {
                $q->whereIn('status', ['Pending', 'Ongoing']);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return view('app.aftap.log_donor._table', compact('log_donors'));
    }

    /**
     * Cari log_donor berdasarkan no_pendaftaran donor (untuk scan barcode).
     */
    public function log_donor_scan(Request $request)
    {
        $request->validate([
            'no_pendaftaran' => 'required|string',
        ]);

        $log_donor = \App\Models\LogDonor::with(['donor', 'aftap'])
            ->whereHas('donor', function ($q) use ($request) {
                $q->where('no_pendaftaran', $request->no_pendaftaran);
            })
            ->whereDate('created_at', date('Y-m-d'))
            ->whereHas('aftap', function ($q) {
                $q->whereIn('status', ['Pending', 'Ongoing']);
            })
            ->latest()
            ->first();

        if (!$log_donor) {
            return response()->json([
                'success' => false,
                'message' => 'No. Pendaftaran tidak ditemukan di antrian aftap hari ini',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'id'      => $log_donor->id,
            'nama'    => $log_donor->donor->nama,
        ]);
    }

    public function log_donor_show($id)
    {
        $log_donor = \App\Models\LogDonor::with([
            'donor',
            'donor.asalDarah',
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

    /**
     * Update asal darah untuk donor + aftap terkait log_donor tertentu.
     */
    public function update_asal_darah(Request $request, $id)
    {
        $request->validate([
            'asal_darah_id' => 'required|exists:asal_darah,id',
        ]);

        $logDonor  = \App\Models\LogDonor::with(['donor', 'aftap', 'pemeriksaanDokter'])->findOrFail($id);
        $asalDarah = \App\Models\AsalDarah::findOrFail($request->asal_darah_id);

        // update tabel donor
        $logDonor->donor->update([
            'asal_darah_id'   => $asalDarah->id,
            'nama_asal_darah' => $asalDarah->nama,
        ]);

        // update tabel aftap (jika record aftap sudah ada)
        if ($logDonor->aftap) {
            $logDonor->aftap->update([
                'asal_darah_id' => $asalDarah->id,
            ]);
        }

        // sinkronkan juga field teks bebas di pemeriksaan_dokter (dipakai form aftap)
        if ($logDonor->pemeriksaanDokter) {
            $logDonor->pemeriksaanDokter->update([
                'asal_darah' => $asalDarah->nama,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Asal darah berhasil diperbarui',
            'data'    => [
                'asal_darah_id' => $asalDarah->id,
                'nama'          => $asalDarah->nama,
            ],
        ]);
    }

    public function update_tipe_kantong(Request $request, $id)
{
    $request->validate([
        'tipe_kantong_id' => 'required|exists:tipe_kantong,id',
    ]);

    $logDonor = \App\Models\LogDonor::with('pemeriksaanDokter')->findOrFail($id);

    if (!$logDonor->pemeriksaanDokter) {
        return response()->json([
            'success' => false,
            'message' => 'Data pemeriksaan dokter untuk log donor ini belum tersedia',
        ], 422);
    }

    $logDonor->pemeriksaanDokter->update([
        'tipe_kantong_id' => $request->tipe_kantong_id,
    ]);

    $tipeKantong = \App\Models\TipeKantong::with('jenisKantong')->find($request->tipe_kantong_id);

    return response()->json([
        'success' => true,
        'message' => 'Jenis kantong berhasil diperbarui',
        'data'    => [
            'id'   => $tipeKantong->id,
            'text' => optional($tipeKantong->jenisKantong)->nama . ' - ' . $tipeKantong->nama,
        ],
    ]);
}


public function search_tipe_kantong(Request $request)
{
    $q = $request->get('q');

    $data = \App\Models\TipeKantong::with('jenisKantong')
        ->when($q, fn($query) => $query->where('nama', 'like', "%{$q}%"))
        ->limit(20)
        ->get();

    return response()->json([
        'results' => $data->map(fn($item) => [
            'id'   => $item->id,
            'text' => optional($item->jenisKantong)->nama . ' - ' . $item->nama,
        ]),
    ]);
}
    public function search_asal_darah(Request $request)
    {
        $q = $request->get('q');

        $data = \App\Models\AsalDarah::query()
            ->when($q, function ($query) use ($q) {
                $query->where('nama', 'like', "%{$q}%")
                      ->orWhere('kode', 'like', "%{$q}%");
            })
            ->orderBy('nama')
            ->limit(20)
            ->get(['id', 'kode', 'nama']);

        return response()->json([
            'results' => $data->map(fn($item) => [
                'id'   => $item->id,
                'text' => $item->kode . ' - ' . $item->nama,
            ]),
        ]);
    }

    /**
     * Cari petugas (untuk select2 di form aftap).
     */
    public function search_petugas(Request $request)
    {
        $q = $request->get('q');

        $data = \App\Models\Petugas::query()
            ->when($q, fn($query) => $query->where('nama', 'like', "%{$q}%"))
            ->orderBy('nama')
            ->limit(20)
            ->get(['id', 'nama']);

        return response()->json([
            'results' => $data->map(fn($item) => [
                'id'   => $item->id,
                'text' => $item->nama,
            ]),
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