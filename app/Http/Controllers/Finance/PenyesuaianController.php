<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\IoResourceController;
use App\Models\ProgramKerja;
use App\Models\Coa;
use App\Models\Penyesuaian;
use App\Services\PenyesuaianService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PenyesuaianController extends IoResourceController
{
    protected $viewPrefix = 'app.finance.penyesuaian';
    protected $itemVariable = 'penyesuaian';
    protected $service; // Tanpa type declaration

    public function __construct(PenyesuaianService $service)
    {
        $this->service = $service;
    }

    // =======================
    // INDEX
    // =======================
    public function index()
    {
        $penyesuaian = Penyesuaian::orderBy('tgl', 'desc')->paginate(10);
        return view("$this->viewPrefix.index", compact('penyesuaian'));
    }

    // =======================
    // SEARCH
    // =======================
    public function search(Request $request)
    {
        $data = Penyesuaian::query();

        if ($request->kode) $data->where('kode', 'like', "%{$request->kode}%");
        if ($request->program_kerja_id) $data->where('program_kerja_id', $request->program_kerja_id);
        if ($request->nama_akun) $data->where('nama_akun', 'like', "%{$request->nama_akun}%");

        $penyesuaian = $data->orderBy('tgl', 'desc')->paginate($request->paginate ?? 10);

        return view("$this->viewPrefix._table", compact('penyesuaian'));
    }

    // =======================
    // GENERATE KODE OTOMATIS
    // =======================
    private function generateKode()
    {
        $last = Penyesuaian::orderBy('id', 'desc')->first();
        if (!$last) return 'PNY-0001';

        $number = (int) str_replace('PNY-', '', $last->kode);
        $number++;
        return 'PNY-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    // =======================
    // CREATE FORM
    // =======================
    public function create()
    {
        return view("$this->viewPrefix._form", [
            'penyesuaian' => null,
            'programKerjaList' => ProgramKerja::all(),
            'coaList' => Coa::all(),
            'kodeOtomatis' => $this->generateKode(),
        ]);
    }

    // =======================
    // EDIT FORM
    // =======================
    public function edit($id)
    {
        return view("$this->viewPrefix._form", [
            'penyesuaian' => Penyesuaian::findOrFail($id),
            'programKerjaList' => ProgramKerja::all(),
            'coaList' => Coa::all(),
            'kodeOtomatis' => null,
        ]);
    }

    // =======================
    // STORE
    // =======================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode'             => 'required|unique:penyesuaian,kode',
            'program_kerja_id' => 'required|exists:program_kerja,id',
            'dokumen'          => 'nullable|string',
            'ref_bayar'        => 'nullable|string',
            'transaksi_coa'    => 'required|string',
            'nominal_debit'    => 'nullable|numeric',
            'nominal_kredit'   => 'nullable|numeric',
            'keterangan'       => 'required|string',
            'jenis_saldo'      => 'required|string',
            'tgl'              => 'required|date',
            'terima_dari'      => 'nullable|string',
            'dibayarkan_ke'    => 'nullable|string',
            'rekning_kas'      => 'nullable|string',
        ]);

        $this->service->store($validated);

        return redirect()->route('finance.penyesuaian.index')
                         ->with('success', 'Data berhasil ditambahkan.');
    }

    // =======================
    // UPDATE
    // =======================
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'kode'             => ['required', Rule::unique('penyesuaian', 'kode')->ignore($id)],
            'program_kerja_id' => 'required|exists:program_kerja,id',
            'dokumen'          => 'nullable|string',
            'ref_bayar'        => 'nullable|string',
            'transaksi_coa'    => 'required|string',
            'nominal_debit'    => 'nullable|numeric',
            'nominal_kredit'   => 'nullable|numeric',
            'keterangan'       => 'required|string',
            'jenis_saldo'      => 'required|string',
            'tgl'              => 'required|date',
            'terima_dari'      => 'nullable|string',
            'dibayarkan_ke'    => 'nullable|string',
            'rekning_kas'      => 'nullable|string',
        ]);

        $this->service->update($validated, $id);

        return redirect()->route('finance.penyesuaian.index')
                         ->with('success', 'Data berhasil diperbarui.');
    }

    // =======================
    // DELETE
    // =======================
    public function destroy($id)
    {
        $penyesuaian = Penyesuaian::findOrFail($id);
        $this->service->destroy($penyesuaian);

        return back()->with('success', 'Data berhasil dihapus.');
    }
}