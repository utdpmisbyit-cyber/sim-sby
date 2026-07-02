<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\IoResourceController;
use App\Services\CoaService;
use App\Models\Coa;
use Illuminate\Http\Request;

class CoaController extends IoResourceController
{
    protected $service;
    protected $viewPrefix = 'app.finance.coa';
    protected $itemVariable = 'coa';

    public function __construct()
    {
        $this->service = new CoaService();
    }

    // ======================
    // SEARCH
    // ======================
    public function search(Request $request)
    {
        $data = Coa::query();

        if ($request->nama_akun) {
            $data->where('nama_akun', 'like', "%{$request->nama_akun}%");
        }

        if ($request->kd_coa) {
            $data->where('kd_coa', 'like', "%{$request->kd_coa}%");
        }

        $coa = $data->orderBy('kd_coa', 'ASC')
            ->paginate($request->paginate ?? 10);

        return view("$this->viewPrefix._table", compact('coa'));
    }

    // ======================
    // GENERATE KODE
    // ======================
    private function generateKode()
    {
        $last = Coa::orderBy('kd_coa', 'DESC')->first();

        if (!$last) return 'COA0001';

        $num = (int) substr($last->kd_coa, 3);
        return 'COA' . str_pad($num + 1, 4, '0', STR_PAD_LEFT);
    }

    // ======================
    // CREATE FORM
    // ======================
    public function create()
    {
        return view("$this->viewPrefix._form", [
            'coa' => null,
            'kode_otomatis' => $this->generateKode()
        ]);
    }

    // ======================
    // EDIT FORM
    // ======================
    public function edit($kd_coa)
    {
        $coa = Coa::where('kd_coa', $kd_coa)->first();

        if (!$coa) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data COA tidak ditemukan'
            ], 404);
        }

        return view("$this->viewPrefix._form", compact('coa'));
    }

    // ======================
    // STORE
    // ======================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kd_coa'      => 'required|unique:coa,kd_coa',
            'kategori_1'  => 'required',
            'kategori_2'  => 'required',
            'nama_akun'   => 'required',
            'possaldo'    => 'required',
            'poslaporan'  => 'required',
        ]);

        Coa::create($validated);

        return back()->with('success', 'Data COA berhasil ditambahkan.');
    }

    // ======================
    // UPDATE
    // ======================
    public function update(Request $request, $kd_coa)
    {
        $coa = Coa::where('kd_coa', $kd_coa)->firstOrFail();

        $validated = $request->validate([
            'kd_coa'      => "required|unique:coa,kd_coa,$coa->kd_coa,kd_coa",
            'kategori_1'  => 'required',
            'kategori_2'  => 'required',
            'nama_akun'   => 'required',
            'possaldo'    => 'required',
            'poslaporan'  => 'required',
        ]);

        $coa->update($validated);

        return back()->with('success', 'Data COA berhasil diperbarui.');
    }

    // ======================
    // DELETE
    // ======================
    public function destroy($kd_coa)
    {
        Coa::where('kd_coa', $kd_coa)->delete();

        return back()->with('success', 'Data COA berhasil dihapus.');
    }
}