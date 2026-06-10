<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\IoResourceController;
use App\Models\GeneralLeadge;
use App\Models\ProgramKerja;
use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GeneralLedgeController extends IoResourceController
{
    protected $viewPrefix = 'app.finance.general_ledge';
    protected $itemVariable = 'general_ledge';

    // =======================
    // INDEX
    // =======================
    public function index()
    {
        $general_ledge = GeneralLeadge::orderBy('tgl', 'desc')->paginate(10);
        return view("$this->viewPrefix.index", compact('general_ledge'));
    }

    // =======================
    // SEARCH
    // =======================
    public function search(Request $request)
    {
        $data = GeneralLeadge::query();

        if ($request->kode) $data->where('kode', 'like', "%{$request->kode}%");
        if ($request->program_kerja_id) $data->where('program_kerja_id', $request->program_kerja_id);
        if ($request->nama_akun) $data->where('nama_akun', 'like', "%{$request->nama_akun}%");

        $general_ledge = $data->orderBy('tgl', 'desc')->paginate($request->paginate ?? 10);

        return view("$this->viewPrefix._table", compact('general_ledge'));
    }

    // =======================
    // GENERATE KODE OTOMATIS
    // =======================
    private function generateKode()
    {
        $last = GeneralLeadge::orderBy('id', 'desc')->first();
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
            'general_ledge' => null,
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
            'general_ledge' => GeneralLeadge::findOrFail($id),
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
            'kode'             => 'required|unique:general_leadge,kode',
            'program_kerja_id' => 'required|exists:program_kerja,id',
            'dokumen'          => 'nullable|string',
            'ref_bayar'        => 'nullable|string',
            'coa_id'           => 'required|exists:coa,kd_coa', // ← gunakan coa_id
            'nominal_debit'    => 'nullable|numeric',
            'nominal_kredit'   => 'nullable|numeric',
            'keterangan'       => 'required|string',
            'jenis_saldo'      => 'required|string',
            'tgl'              => 'required|date',
        ]);

        GeneralLeadge::create($validated);

        return redirect()->route('finance.general_ledge.index')
                         ->with('success', 'Data berhasil ditambahkan.');
    }

    // =======================
    // UPDATE
    // =======================
    public function update(Request $request, $id)
    {
        $general_ledge = GeneralLeadge::findOrFail($id);

        $validated = $request->validate([
            'kode'             => 'required|unique:general_leadge,kode',
            'program_kerja_id' => 'required|exists:program_kerja,id',
            'dokumen'          => 'nullable|string',
            'ref_bayar'        => 'nullable|string',
            'coa_id'           => 'required|exists:coa,kd_coa', // ← gunakan coa_id
            'nominal_debit'    => 'nullable|numeric',
            'nominal_kredit'   => 'nullable|numeric',
            'keterangan'       => 'required|string',
            'jenis_saldo'      => 'required|string',
            'tgl'              => 'required|date',
        ]);

        $program = ProgramKerja::find($validated['program_kerja_id']);
        $validated['program_kerja'] = $program->nama_program ?? null;
        $coa = Coa::where('nama_akun', $validated['transaksi_coa'])->first();
        $validated['nama_akun'] = $coa->nama_akun ?? null;


        $general_ledge->update($validated);

        return redirect()->route('finance.general_ledge.index')
                         ->with('success', 'Data berhasil diperbarui.');
    }

    // =======================
    // DELETE
    // =======================
    public function destroy($id)
    {
        GeneralLeadge::findOrFail($id)->delete();
        return back()->with('success', 'Data berhasil dihapus.');
    }
}