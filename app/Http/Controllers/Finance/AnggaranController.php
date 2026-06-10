<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\IoResourceController;
use App\Models\Anggaran;
use App\Models\Petugas;
use Illuminate\Http\Request;

class AnggaranController extends IoResourceController
{
    protected $viewPrefix = 'app.finance.anggaran';
    protected $itemVariable = 'anggaran';

    // =======================
    // SEARCH
    // =======================
    public function search(Request $request)
    {
        $data = Anggaran::query();

        if ($request->kode) {
            $data->where('kode', 'like', "%{$request->kode}%");
        }

        if ($request->tahun_anggaran) {
            $data->where('tahun_anggaran', 'like', "%{$request->tahun_anggaran}%");
        }

        $anggaran = $data->orderBy('id', 'DESC')
            ->paginate($request->paginate ?? 10);

        return view("$this->viewPrefix._table", compact('anggaran'));
    }

    // =======================
    // GENERATE KODE
    // =======================
    private function generateKode()
    {
        $last = Anggaran::orderBy('id', 'DESC')->first();

        $num = $last ? $last->id + 1 : 1;

        return 'ANG-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    // =======================
    // CREATE
    // =======================
     public function create()
    {
        $petugasList = Petugas::all();

        // Generate kode otomatis
        $tahun = now()->year;
        $lastAnggaran = Anggaran::whereYear('tgl_input', $tahun)
                                ->orderBy('id', 'desc')
                                ->first();

        if ($lastAnggaran) {
            $lastNumber = (int) substr($lastAnggaran->kode, -3);
            $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '001';
        }

        $kodeOtomatis = "ANG-{$tahun}-{$nextNumber}";

        return view('app.finance.anggaran._form', compact('petugasList', 'kodeOtomatis'));
    }

    // FORM EDIT
    public function edit($id)
    {
        $anggaran = Anggaran::findOrFail($id);
        $petugasList = Petugas::orderBy('nama')->get();
        return view($this->viewPrefix . '._form', compact('anggaran', 'petugasList'));
    }

    // =======================
    // STORE
    // =======================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode'            => 'required|unique:anggaran,kode',
            'tgl_input'       => 'required|date',
            'tahun_anggaran'  => 'required',
            'keterangan'      => 'nullable',
            'nilai_anggaran'  => 'required|numeric',
            'user_input'      => 'required',
        ]);

        Anggaran::create($validated);

        return back()->with('success', 'Data Anggaran berhasil ditambahkan.');
    }

    // =======================
    // UPDATE
    // =======================
    public function update(Request $request, $id)
    {
        $anggaran = Anggaran::findOrFail($id);

        $validated = $request->validate([
            'kode'            => "required|unique:anggaran,kode,$id,id",
            'tgl_input'       => 'required|date',
            'tahun_anggaran'  => 'required',
            'keterangan'      => 'nullable',
            'nilai_anggaran'  => 'required|numeric',
            'user_input'      => 'required',
        ]);

        $anggaran->update($validated);

        return back()->with('success', 'Data Anggaran berhasil diperbarui.');
    }

    // =======================
    // DELETE
    // =======================
    public function destroy($id)
    {
        Anggaran::findOrFail($id)->delete();

        return back()->with('success', 'Data Anggaran berhasil dihapus.');
    }
}