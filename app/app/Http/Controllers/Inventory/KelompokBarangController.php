<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\IoResourceController;
use App\Services\KelompokBarangService;
use App\Models\KelompokBarang;
use Illuminate\Http\Request;

class KelompokBarangController extends IoResourceController
{
    protected $service;
    protected $viewPrefix;
    protected $itemVariable;

    public function __construct()
    {
        $this->service      = new KelompokBarangService();
        $this->viewPrefix   = 'app.inventory.kelompok_barang';
        $this->itemVariable = 'kelompok_barang';
    }

    // ==========================
    // SEARCH TABLE
    // ==========================
    public function search(Request $request)
    {
        $data = KelompokBarang::query();

        if ($request->nama) {
            $data->where('nama', 'like', "%{$request->nama}%");
        }
        if ($request->kode) {
            $data->where('kode', 'like', "%{$request->kode}%");
        }

        $kelompok_barang = $data->orderByDesc('id')
            ->paginate($request->paginate ?? 10);

        return view("$this->viewPrefix._table", compact('kelompok_barang'));
    }

    // ==========================
    // CREATE VIEW
    // ==========================
    public function create()
    {
        return view("$this->viewPrefix._form", [
            'kelompok_barang' => null,
            'kode_otomatis'   => $this->generateKode()
        ]);
    }

    // ==========================
    // EDIT VIEW
    // ==========================
    public function edit($id)
    {
        return view("$this->viewPrefix._form", [
            'kelompok_barang' => KelompokBarang::findOrFail($id)
        ]);
    }

    // ==========================
    // STORE
    // ==========================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|unique:kelompok_barang,kode',
            'nama' => 'required',
        ]);

        KelompokBarang::create([
            'kode' => $request->kode,
            'nama' => $request->nama,
        ]);

        return back()->with('success', 'Kelompok barang berhasil ditambahkan.');
    }

    // ==========================
    // UPDATE
    // ==========================
    public function update(Request $request, $id)
    {
        $data = KelompokBarang::findOrFail($id);

        $validated = $request->validate([
            'kode' => "required|unique:kelompok_barang,kode,$id",
            'nama' => 'required',
        ]);

        $data->update([
            'kode' => $request->kode,
            'nama' => $request->nama,
        ]);

        return back()->with('success', 'Kelompok barang berhasil diupdate.');
    }

    // ==========================
    // DELETE
    // ==========================
    public function destroy($id)
    {
        KelompokBarang::findOrFail($id)->delete();
        return back()->with('success', 'Data berhasil dihapus.');
    }

    // ==========================
    // GENERATE KODE OTOMATIS
    // ==========================
    private function generateKode()
    {
        $last = KelompokBarang::orderByDesc('id')->first();
        $nomor = $last ? $last->id + 1 : 1;
        return 'KB' . str_pad($nomor, 4, '0', STR_PAD_LEFT);
    }
}