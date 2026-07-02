<?php

namespace App\Http\Controllers\MobilUnit;

use App\Http\Controllers\Controller;
use App\Services\DonorService;
use App\Models\Donor;
use Illuminate\Http\Request;
use App\Services\LogDonorService;


class PendaftaranMobilController extends Controller
{
    protected $logDonorService;
    protected $service;
    public function __construct()
    {
        $this->logDonorService = new LogDonorService();
        $this->service = new DonorService();
    }

    public function index()
    {
        $params = '';
         $steps = $this->logDonorService->next_step;
        return view('app.mobil_unit.pendaftaran.index', compact('params','steps'));
    }
        public function search(Request $request)
    {
        $date = date('Y-m-d');
       $paramsLogDonor = [
            'date' => $date,
            'jenis_donor' => 'mobil unit',
            'with' => ['donor', 'petugasRegistrasi']
        ];

        $log_donors = $this->logDonorService->search($paramsLogDonor);
        $paramsDonor = [
            'date' => $date
        ];

        $steps = $this->logDonorService->next_step;
        $count_steps = [];
        $donors = $this->service->search($request->all());
        foreach ($steps as $step) $count_steps[$step] = $this->logDonorService->search(['date' => $date, 'step' => $step, 'count' => 1]);

        return view('app.mobil_unit.pendaftaran._table', compact('log_donors', 'steps', 'count_steps','donors'));
    }
        public function search_donor(Request $request)
    {
        $donorService = new DonorService();
        $donors = $donorService->search(['search' => $request->input('search') ?? '-', 'limit' => 10]);

        return view('app.mobil_unit.pendaftaran._table_donor', compact('donors'));
    }
 
    public function create()
    {
        $donor = null;
        return view('app.mobil_unit.donor._form', $this->view_data($donor));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        if (isset($data['penghargaan']) && is_array($data['penghargaan'])) {
            $data['penghargaan'] = implode(',', $data['penghargaan']);
        } else {
            $data['penghargaan'] = null;
        }

        $data['kode']           = $this->service->generateKode();
        $data['no_pendaftaran'] = $this->service->generateNoPendaftaran();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('donor/foto', 'public');
        } elseif (!empty($data['foto_base64']) && $data['foto_base64'] !== 'hapus') {
            $data['foto'] = $this->saveBase64Foto($data['foto_base64']);
        }
        unset($data['foto_base64']);

        $donor = $this->service->find($request->donor_id);

        // ★ Deklarasi SEBELUM store() agar bisa dipakai di response
        $kode_log = $this->logDonorService->autoKode();
        
        $this->logDonorService->store([
            'kode'                  => $kode_log,
            'cabang_id'             => session('active_cabang.id'),
            'donor_id'              => $donor->id,
            'petugas_registrasi_id' => auth()->user()->petugas->id ?? null,
            'step'                  => 'Registrasi',
            'jenis_donor'           => 'mobil unit',

        ]);
        $totalDonor = $this->logDonorService->countByDonor($donor->id);
        $donor->update([
            'donor_ke' => $totalDonor
        ]);
        return response()->json([
            'status'  => true,
            'message' => 'Data berhasil disimpan',
            'tiket'   => [
                'kode'       => $kode_log,
                'nama'       => $donor->nama,
                'kode_donor' => $donor->kode,
                'no_hp'      => $donor->no_telp,
                'no_ktp'     => $donor->no_ktp,
                'golongan'   => $donor->golongan_darah . ' ' . ($donor->rhesus_caption ?? ''),
                'waktu'      => now()->format('d/m/Y H:i'),
                'cabang'     => session('active_cabang.nama') ?? '-',
                'petugas'    => auth()->user()->petugas->nama ?? '-',
            ],
        ]);
    }

    public function edit($id)
    {
        $donor = $this->service->find($id);
        return view('app.mobil_unit.donor._form', $this->view_data($donor));
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        if (isset($data['penghargaan']) && is_array($data['penghargaan'])) {
            $data['penghargaan'] = implode(',', $data['penghargaan']);
        }
        if (!isset($data['penghargaan'])) {
            $data['penghargaan'] = null;
        }
        // Handle foto update
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('donor/foto', 'public');
        } elseif (!empty($data['foto_base64']) && $data['foto_base64'] === 'hapus') {
            $data['foto'] = null;
        } elseif (!empty($data['foto_base64']) && $data['foto_base64'] !== 'hapus') {
            $data['foto'] = $this->saveBase64Foto($data['foto_base64']);
        }
        unset($data['foto_base64']);

        // Jangan update kode & no_pendaftaran
        unset($data['kode'], $data['no_pendaftaran']);

        $this->service->update($id, $data);

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil diupdate'
        ]);
    }

    public function destroy($id)
    {
        return $this->service->delete($id);
    }

    /**
     * API: Generate kode dan no_pendaftaran baru (untuk form tambah donor).
     * GET /unit/donor/generate_kode
     */
    public function generate_kode()
    {
        return response()->json([
            'kode'           => $this->service->generateKode(),
            'no_pendaftaran' => $this->service->generateNoPendaftaran(),
        ]);
    }

    /**
     * API: Hitung donor_ke berdasarkan no_ktp.
     * GET /unit/donor/get_donor_ke?no_ktp=xxx
     */
    public function get_donor_ke(Request $request)
    {
        $no_ktp = $request->input('no_ktp', '');
        if (empty($no_ktp)) return response()->json(['donor_ke' => 1, 'golongan_darah' => null, 'rhesus' => null]);
        return response()->json($this->service->getDonorKeByKtp($no_ktp));
    }

    // ─── Helper: view data untuk form ──────────────────────────────────────────
    private function view_data($donor): array
    {
        $jenis_kelamin_options      = \App\Models\Donor::JENIS_KELAMIN;
        $agama_options              = \App\Models\Donor::AGAMA;
        $golongan_darah_options     = \App\Models\Donor::GOLONGAN_DARAH;
        $rhesus_options             = \App\Models\Donor::RHESUS;
        $golongan_darah_lain_options = \App\Models\Donor::GOLONGAN_DARAH_LAIN;

        $pekerjaan_options      = \App\Models\Pekerjaan::pluck('nama', 'id')->toArray();
        $kewarganegaraan_options = \App\Models\Kewarganegaraan::pluck('nama', 'id')->toArray();
        $wilayah_options        = \App\Models\Wilayah::pluck('nama', 'id')->toArray();
        $kecamatan_options      = \App\Models\Kecamatan::pluck('nama', 'id')->toArray();

        return compact(
            'donor',
            'jenis_kelamin_options', 'agama_options',
            'golongan_darah_options', 'rhesus_options', 'golongan_darah_lain_options',
            'pekerjaan_options', 'kewarganegaraan_options',
            'wilayah_options', 'kecamatan_options'
        );
    }

    // ─── Helper: simpan foto base64 ke storage ──────────────────────────────────
    private function saveBase64Foto(string $base64): string
    {
        // Format: data:image/jpeg;base64,XXXXX
        $parts    = explode(',', $base64);
        $imgData  = base64_decode($parts[1] ?? $parts[0]);
        $filename = 'donor/foto/' . uniqid('donor_', true) . '.jpg';
        \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $imgData);
        return $filename;
    }
}
