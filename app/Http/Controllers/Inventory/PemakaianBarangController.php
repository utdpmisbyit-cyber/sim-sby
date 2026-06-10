<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\IoResourceController;
use App\Services\PemakaianBarangService;
use App\Models\PemakaianBarang;
use App\Models\Barang;
use App\Models\BagianPetugas;
use Illuminate\Http\Request;
use App\Models\PengajuanBarang;

class PemakaianBarangController extends IoResourceController
{
    protected $service;
    protected $viewPrefix;
    protected $itemVariable;

    public function __construct()
    {
        $this->service      = new PemakaianBarangService();
        $this->viewPrefix   = 'app.inventory.pemakaian_barang';
        $this->itemVariable = 'pemakaian_barang';
    }

    public function search(Request $request)
    {
        $query = PemakaianBarang::with(['barang', 'pengajuanBarang']);

        if ($request->nama) {
            $query->where('nama_barang', 'like', '%' . $request->nama . '%');
        }

        if ($request->kode) {
            $query->where('kode', 'like', '%' . $request->kode . '%');
        }

        $pemakaian_barang = $query->orderByDesc('id')
            ->paginate($request->paginate ?? 10);

        return view($this->viewPrefix . '._table', compact('pemakaian_barang'));
    }

     public function create()
    {
        return view($this->viewPrefix . '._form', [
            'pemakaian_barang' => null,
            'barang' => Barang::all(),
            'bagian' => BagianPetugas::all(),
            'pengajuan' => PengajuanBarang::all(),
            'kode_otomatis' => $this->generateKode(),
        ]);
    }

    public function edit($id)
    {
        return view($this->viewPrefix . '._form', [
            'pemakaian_barang' => PemakaianBarang::findOrFail($id),
            'barang' => Barang::all(),
            'bagian' => BagianPetugas::all(),
            'pengajuan' => PengajuanBarang::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_pemakaian' => 'required|date',
            'barang_id' => 'required',
            'bagian_id' => 'required',
            'jumlah_pakai' => 'required|numeric',
        ]);

        PemakaianBarang::create([
            'kode' => $this->generateKode(),
            'tgl_pemakaian' => $request->tgl_pemakaian,
            'barang_id' => $request->barang_id,
            'pengajuan_barang_id' => $request->pengajuan_barang_id,
            'bagian_id' => $request->bagian_id,
            'nama_barang' => $request->nama_barang, 
            'jumlah_pakai' => $request->jumlah_pakai,
            'keterangan' => $request->keterangan,
            'user_input' => auth()->user()->name ?? 'Admin',
        ]);

       return redirect()->route('inventory.pemakaian_barang.index')
             ->with('success', 'Data berhasil ditambahkan');
    }

    public function update(Request $request, $id)
{
    $data = PemakaianBarang::findOrFail($id);

    $data->update([
        'tgl_pemakaian' => $request->tgl_pemakaian,
        'barang_id' => $request->barang_id,
        'bagian_id' => $request->bagian_id,
        'pengajuan_barang_id' => $request->pengajuan_barang_id, 
        'nama_barang' => $request->nama_barang,
        'jumlah_pakai' => $request->jumlah_pakai,
        'keterangan' => $request->keterangan,
    ]);
     return redirect()->route('inventory.pemakaian_barang.index')
    ->with('success', 'Data berhasil diupdate');
}

    public function destroy($id)
    {
        PemakaianBarang::findOrFail($id)->delete();
       return redirect()->route('inventory.pemakaian_barang.index')
          ->with('success', 'Data berhasil dihapus');
    }
    private function generateKode()
    {
        $last = PemakaianBarang::orderByDesc('kode')->first();

        if (!$last) {
            return 'P' . date('Ymd') . '0001';
        }

        $lastNumber = (int) substr($last->kode, -4);
        $newNumber = $lastNumber + 1;

        return 'P' . date('Ymd') . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}