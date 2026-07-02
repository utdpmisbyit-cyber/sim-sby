<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\IoResourceController;
use App\Models\KasKeluar;
use App\Models\ProgramKerja;
use App\Models\Rekanan;
use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\KasKeluarService; 

class KasKeluarController extends IoResourceController
{
    protected $service;
    protected $viewPrefix = 'app.finance.kas_keluar';
    protected $itemVariable = 'kas_keluar';

    public function __construct(KasKeluarService $service)
    {
        $this->service = $service;
    }

    // =======================
    // INDEX
    // =======================
    public function index()
    {
        $kas_keluar = KasKeluar::orderBy('tgl', 'desc')->paginate(10);
        return view("$this->viewPrefix.index", compact('kas_keluar'));
    }

    // =======================
    // SEARCH
    // =======================
    public function search(Request $request)
    {
        $data = KasKeluar::query();

        if ($request->kode) {
            $data->where('kode', 'like', "%{$request->kode}%");
        }
        if ($request->program_kerja_id) {
            $data->where('program_kerja_id', $request->program_kerja_id);
        }
        if ($request->nama_akun) {
            $data->where('nama_akun', 'like', "%{$request->nama_akun}%");
        }

        $kas_keluar = $data->orderBy('tgl', 'desc')->paginate($request->paginate ?? 10);

        return view("$this->viewPrefix._table", compact('kas_keluar'));
    }

    // =======================
    // GENERATE KODE
    // =======================
    private function generateKode()
    {
        $last = KasKeluar::orderBy('id', 'desc')->first();
        if (!$last) return 'KK-0001';

        $number = (int) str_replace('KK-', '', $last->kode);
        return 'KK-' . str_pad($number + 1, 4, '0', STR_PAD_LEFT);
    }

    // =======================
    // CREATE
    // =======================
    public function create()
    {
        return view("$this->viewPrefix._form", [
            'kas_keluar'      => null,
            'programKerjaList'=> ProgramKerja::all(),
            'rekananList'     => Rekanan::all(),
            'coaList'         => Coa::all(),
            'kodeOtomatis'    => $this->generateKode(),
        ]);
    }

    // =======================
    // EDIT
    // =======================
    public function edit($id)
    {
        return view("$this->viewPrefix._form", [
            'kas_keluar'      => KasKeluar::findOrFail($id),
            'programKerjaList'=> ProgramKerja::all(),
            'rekananList'     => Rekanan::all(),
            'coaList'         => Coa::all(),
            'kodeOtomatis'    => null,
        ]);
    }

    // =======================
    // STORE
    // =======================
    public function store(Request $request)
    {
        $validated = $this->validateKasKeluar($request);

        $this->service->store($validated);

        return redirect()->route('finance.kas_keluar.index')
                         ->with('success', 'Data Kas Keluar + Jurnal + Trial Balance berhasil ditambahkan.');
    }

    // =======================
    // UPDATE
    // =======================
    public function update(Request $request, $id)
    {
        $validated = $this->validateKasKeluar($request, $id);

        $this->service->updateKasKeluar($validated, $id);

        return redirect()->route('finance.kas_keluar.index')
                         ->with('success', 'Data Kas Keluar + Jurnal + Trial Balance berhasil diperbarui.');
    }

    // =======================
    // DELETE
    // =======================
    public function destroy($id)
    {
        $this->service->deleteKasKeluar($id);

        return back()->with('success', 'Data Kas Keluar + Jurnal + Trial Balance berhasil dihapus.');
    }

    // =======================
    // VALIDATION REUSABLE
    // =======================
    private function validateKasKeluar(Request $request, $id = null)
    {
        return $request->validate([
            'kode'             => ['required', Rule::unique('kas_keluar')->ignore($id)],
            'program_kerja_id' => 'required|exists:program_kerja,id',
            'dokumen'          => 'nullable|string',
            'ref_an'           => 'nullable|string',
            'rekning_kas'      => 'required|string',
            'dibayar_ke'       => 'required|exists:rekanan,nama_rekanan',
            'nominal'          => 'required|numeric',
            'keterangan'       => 'required|string',
            'tgl'              => 'required|date',
            'nama_akun'        => 'required|exists:coa,nama_akun',
        ]);
    }
}