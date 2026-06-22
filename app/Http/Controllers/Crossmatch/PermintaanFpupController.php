<?php

namespace App\Http\Controllers\Crossmatch;

use App\Http\Controllers\Controller;
use App\Models\Diagnosa;
use App\Models\PermintaanFpup;
use App\Models\TujuanDarah;
use App\Services\PermintaanFpupService;
use Illuminate\Http\Request;

class PermintaanFpupController extends Controller
{
    protected PermintaanFpupService $service;

    private string $view = 'app.crossmatch.permintaan_fpup';

    public function __construct()
    {
        $this->service = new PermintaanFpupService();
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'tgl', 'per_page']);
        $data    = $this->service->list($filters);

        return view("{$this->view}.index", array_merge(
            ['data' => $data, 'filters' => $filters],
            $this->service->constants()
        ));
    }

    public function barcode($id)
    {
        $fpup = PermintaanFpup::findOrFail($id);

        return view('app.crossmatch.permintaan_fpup.barcode', compact('fpup'));
    }

    public function create()
    {
        return view("{$this->view}.form", array_merge(
            [
                'fpup' => null,
                'mode' => 'create',
                'nextNoReg' => $this->service->generateNoRegistrasi(),
                'nextNoRegOnline' => $this->service->generateNoRegistrasiOnline(),
            ],
            $this->service->constants()
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fpup_id'                => 'nullable|exists:fpup,id',
            'nama_pasien'            => 'required|string|max:100',
            'tgl_lahir'              => 'nullable|date',
            'umur'                   => 'nullable|integer',
            'kebangsaan'             => 'nullable|string',
            'jenis_kelamin'          => 'nullable|string',
            'alamat'                 => 'nullable|string',
            'kode_rs'                => 'nullable|string|max:20',
            'nama_rs'                => 'nullable|string|max:150',
            'jenis_rs'               => 'nullable|string',
            'kategori_rs'            => 'nullable|string',
            'bagian'                 => 'nullable|string',
            'kelas_rawat'            => 'nullable|string',
            'jns_permintaan'         => 'nullable|string',
            'diagnosa_klinis'        => 'nullable|string',
            'hb'                     => 'nullable|string',
            'alasan_transfusi'       => 'nullable|string',
            'transfusi_sebelumnya'   => 'nullable|boolean',
            'transfusi_kapan'        => 'nullable|date',
            'reaksi_transfusi'       => 'nullable|boolean',
            'reaksi_gejala'          => 'nullable|string',
            'pernah_serologi'        => 'nullable|boolean',
            'serologi_kapan'         => 'nullable|date',
            'serologi_dimana'        => 'nullable|string',
            'serologi_hasil'         => 'nullable|string',
            'cara_pembayaran'        => 'nullable|string',
            'jns_biaya'              => 'nullable|string',
            'jns_donor'              => 'nullable|string',
            'jml_donor'              => 'nullable|integer',
            'nama_dokter'            => 'nullable|string',
            'no_reg'                 => 'nullable|string|max:50',
            'no_reg_online'          => 'nullable|string|max:50',
            'tgl_registrasi_online'  => 'nullable|date',

            'details'                => 'nullable|array',
            'details.*.jns_darah'    => 'required|string',
            'details.*.gol_darah'    => 'nullable|string',
            'details.*.rhesus'       => 'nullable|string',
            'details.*.jumlah'       => 'nullable|integer',
            'details.*.cc'           => 'nullable|integer',
            'details.*.tgl_perlu'    => 'nullable|date',
            'details.*.keterangan'   => 'nullable|string',
        ]);

        $fpup = $this->service->store($validated);

        return redirect()
            ->route('crossmatch.permintaan_fpup.show', $fpup)
            ->with('success', "FPUP {$fpup->no_fpup} berhasil dibuat!");
    }

    public function show(PermintaanFpup $permintaan_fpup)
    {
        $fpup = $permintaan_fpup->load('details', 'fpup');
        return view("{$this->view}.show", array_merge(
            ['fpup' => $fpup],
            $this->service->constants()
        ));
    }

    public function edit(PermintaanFpup $permintaan_fpup)
    {
        $fpup = $permintaan_fpup->load('details', 'fpup');
        return view("{$this->view}.form", array_merge(
            ['fpup' => $fpup, 'mode' => 'edit'],
            $this->service->constants()
        ));
    }

    public function update(Request $request, PermintaanFpup $permintaan_fpup)
    {
        $validated = $request->validate([
            'fpup_id'               => 'nullable|exists:fpup,id',
            'nama_pasien'           => 'required|string|max:100',
            'transfusi_sebelumnya'  => 'nullable|boolean',
            'transfusi_kapan'       => 'nullable|date',
            'no_reg'                => 'nullable|string|max:50',
            'no_reg_online'         => 'nullable|string|max:50',
            'tgl_registrasi_online' => 'nullable|date',
            'reaksi_transfusi'      => 'nullable|boolean',
            'reaksi_gejala'         => 'nullable|string',
            'pernah_serologi'       => 'nullable|boolean',
            'serologi_kapan'        => 'nullable|date',
            'serologi_dimana'       => 'nullable|string',
            'serologi_hasil'        => 'nullable|string',
            'details'               => 'nullable|array',
            'details.*.jns_darah'   => 'required|string',
        ]);

        $this->service->update($permintaan_fpup, $validated);

        return redirect()
            ->route('unit.bank_darah.permintaan_fpup.show', $permintaan_fpup)
            ->with('success', "FPUP {$permintaan_fpup->no_fpup} berhasil diupdate!");
    }

    public function destroy(PermintaanFpup $permintaan_fpup)
    {
        $no = $permintaan_fpup->no_fpup;
        $this->service->destroy($permintaan_fpup);

        return redirect()
            ->route('crossmatch.permintaan_fpup.index')
            ->with('success', "FPUP {$no} berhasil dihapus.");
    }

    public function nextNoFpup()
    {
        return response()->json([
            'no_fpup' => $this->service->generateNoFpup(),
        ]);
    }

    public function updateStatus(Request $request, PermintaanFpup $permintaan_fpup)
    {
        $request->validate(['status' => 'required|in:baru,proses,selesai,batal']);
        $permintaan_fpup->update(['status' => $request->status]);

        return response()->json(['success' => true, 'status' => $request->status]);
    }

    public function searchRs(Request $request)
    {
        $q    = $request->get('q', '');
        $rows = TujuanDarah::with('kelompokRumahSakit')
            ->where(function ($qb) use ($q) {
                $qb->where('nama',  'like', "%{$q}%")
                   ->orWhere('kode', 'like', "%{$q}%");
            })
            ->orderBy('nama')
            ->limit(30)
            ->get(['id', 'kode', 'nama', 'kelompok_rumah_sakit_id']);

        return response()->json($rows->map(function ($r) {
            return [
                'kode_rs'       => $r->kode,
                'nama_rs'       => $r->nama,
                'jenis_rs'      => '',
                'kategori_rs'   => '',
                'kelompok_nama' => $r->kelompokRumahSakit?->nama ?? '',
            ];
        }));
    }

    public function rsByKode(Request $request)
    {
        $kode = $request->get('kode', '');
        $rs   = TujuanDarah::with('kelompokRumahSakit')
            ->where('kode', $kode)
            ->first();

        if (! $rs) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found'         => true,
            'kode_rs'       => $rs->kode,
            'nama_rs'       => $rs->nama,
            'jenis_rs'      => '',
            'kategori_rs'   => '',
            'kelompok_nama' => $rs->kelompokRumahSakit?->nama ?? '',
        ]);
    }

    public function listDiagnosa(Request $request)
    {
        $q    = $request->get('q', '');
        $rows = Diagnosa::when($q, fn ($qb) => $qb->where('nama', 'like', "%{$q}%"))
            ->orderBy('nama')
            ->limit(50)
            ->get(['id', 'kode', 'nama']);

        return response()->json($rows);
    }
}