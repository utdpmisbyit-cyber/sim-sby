<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\IoResourceController;
use App\Models\TrialBalance;
use App\Models\Coa;
use Illuminate\Http\Request;

class TrialBalanceController extends IoResourceController
{
    protected $viewPrefix = 'app.finance.trial_balance';
    protected $itemVariable = 'trial_balance';

    // =======================
    // INDEX
    // =======================
    public function index()
    {
        // Ambil semua kolom penting dan relasi coa
        $trial_balance = TrialBalance::with('coa')
            ->select([
                'id', 'kode', 'coa_id', 'nama_akun', 'sa_debet', 'sa_kredit',
                'debet', 'kredit', 'neraca_debet', 'neraca_kredit',
                'kategori1', 'kategori2', 'pos_saldo', 'pos_laporan', 'created_at'
            ])
            ->orderBy('kode')
            ->get();

        return view("$this->viewPrefix.index", compact('trial_balance'));
    }

    // =======================
    // SEARCH
    // =======================
    public function search(Request $request)
    {
        $data = TrialBalance::with('coa')
            ->select([
                'id', 'kode', 'coa_id', 'nama_akun', 'sa_debet', 'sa_kredit',
                'debet', 'kredit', 'neraca_debet', 'neraca_kredit',
                'kategori1', 'kategori2', 'pos_saldo', 'pos_laporan', 'created_at'
            ])
            ->orderBy('kode');

        if ($request->kode)      $data->where('kode', 'like', "%{$request->kode}%");
        if ($request->nama_akun) $data->where('nama_akun', 'like', "%{$request->nama_akun}%");
        if ($request->kategori1) $data->where('kategori1', $request->kategori1);

        $trial_balance = $data->get();

        return view("$this->viewPrefix._table", compact('trial_balance'));
    }

    // =======================
    // CREATE FORM
    // =======================
    public function create()
    {
        return view("$this->viewPrefix._form", [
            'trial_balance' => null,
            'coaList'       => Coa::all(),
        ]);
    }

    // =======================
    // EDIT FORM
    // =======================
    public function edit($id)
    {
        return view("$this->viewPrefix._form", [
            'trial_balance' => TrialBalance::findOrFail($id),
            'coaList'       => Coa::all(),
        ]);
    }

    // =======================
    // STORE
    // =======================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode'          => 'required|unique:trial_balance,kode',
            'coa_id'        => 'required',
            'nama_akun'     => 'nullable|string',
            'sa_debet'      => 'nullable|numeric',
            'sa_kredit'     => 'nullable|numeric',
            'debet'         => 'nullable|numeric',
            'kredit'        => 'nullable|numeric',
            'laba_debet'    => 'nullable|numeric',
            'laba_kredit'   => 'nullable|numeric',
            'neraca_debet'  => 'nullable|numeric',
            'neraca_kredit' => 'nullable|numeric',
            'kategori1'     => 'nullable|string',
            'kategori2'     => 'nullable|string',
            'pos_saldo'     => 'nullable|string',
            'pos_laporan'   => 'nullable|string',
        ]);

        TrialBalance::create($validated);

        return redirect()->route('finance.trial_balance.index')
                         ->with('success', 'Data berhasil ditambahkan.');
    }

    // =======================
    // UPDATE
    // =======================
    public function update(Request $request, $id)
    {
        $item = TrialBalance::findOrFail($id);

        $validated = $request->validate([
            'kode'          => 'required|unique:trial_balance,kode,' . $id,
            'coa_id'        => 'required',
            'nama_akun'     => 'nullable|string',
            'sa_debet'      => 'nullable|numeric',
            'sa_kredit'     => 'nullable|numeric',
            'debet'         => 'nullable|numeric',
            'kredit'        => 'nullable|numeric',
            'laba_debet'    => 'nullable|numeric',
            'laba_kredit'   => 'nullable|numeric',
            'neraca_debet'  => 'nullable|numeric',
            'neraca_kredit' => 'nullable|numeric',
            'kategori1'     => 'nullable|string',
            'kategori2'     => 'nullable|string',
            'pos_saldo'     => 'nullable|string',
            'pos_laporan'   => 'nullable|string',
        ]);

        $item->update($validated);

        return redirect()->route('finance.trial_balance.index')
                         ->with('success', 'Data berhasil diperbarui.');
    }

    // =======================
    // DELETE
    // =======================
    public function destroy($id)
    {
        TrialBalance::findOrFail($id)->delete();
        return back()->with('success', 'Data berhasil dihapus.');
    }
}