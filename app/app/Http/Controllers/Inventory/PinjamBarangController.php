<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\IoResourceController;
use App\Services\PinjamBarangService;
use App\Models\PinjamBarang;
use App\Models\Barang;
use App\Models\BagianPetugas;
use App\Models\Petugas;
use Illuminate\Http\Request;

class PinjamBarangController extends IoResourceController
{
    protected $service;
    protected $viewPrefix;
    protected $itemVariable;

    public function __construct()
    {
        $this->service      = new PinjamBarangService();
        $this->viewPrefix   = 'app.inventory.pinjam_barang';
        $this->itemVariable = 'pinjam_barang';
    }

    /* ===========================
       SEARCH TABLE
    ============================*/
    public function search(Request $request)
    {
        $query = PinjamBarang::with(['barang', 'petugas', 'bagian']);

        if ($request->nama) {
            $query->whereHas('barang', function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->nama}%");
            });
        }

        if ($request->kode) {
            $query->where('kode', 'like', "%{$request->kode}%");
        }

        $pinjam_barang = $query->orderByDesc('id')
            ->paginate($request->paginate ?? 10);

        return view($this->viewPrefix . '._table', compact('pinjam_barang'));
    }

    /* ===========================
       CREATE FORM
    ============================*/
    public function create()
    {
        return view($this->viewPrefix . '._form', [
            'pinjam_barang' => null,
            'barang'  => Barang::all(),
            'bagian'  => BagianPetugas::all(),
            'petugas' => Petugas::all(),
            'kode_otomatis' => $this->generateKode(),
        ]);
    }

    /* ===========================
       EDIT FORM
    ============================*/
    public function edit($id)
    {
        return view($this->viewPrefix . '._form', [
            'pinjam_barang' => PinjamBarang::findOrFail($id),
            'barang'  => Barang::all(),
            'bagian'  => BagianPetugas::all(),
            'petugas' => Petugas::all(),
        ]);
    }

    /* ===========================
       STORE DATA
    ============================*/
    public function store(Request $request)
    {
        $validated = $request->validate([
            'barang_id'      => 'required',
            'petugas_id'     => 'required',
            'jumlah_pinjam'  => 'required|numeric|min:1',
            'bagian_id'      => 'required',
            'tanggal_pinjam' => 'required|date',
        ]);

        PinjamBarang::create([
            'kode'          => $this->generateKode(),
            'barang_id'     => $request->barang_id,
            'petugas_id'    => $request->petugas_id,
            'jumlah_pinjam' => $request->jumlah_pinjam,
            'bagian_id'     => $request->bagian_id,
            'tanggal_pinjam'=> $request->tanggal_pinjam,
            'keterangan'    => $request->keterangan,
            'diserahkan_ke' => $request->diserahkan_ke,
            'status'        => $request->status ?? 'dipinjam',
        ]);

        return redirect()->route('inventory.pinjam_barang.index')
            ->with('success', 'Data berhasil ditambahkan');
    }

    /* ===========================
       UPDATE DATA
    ============================*/
    public function update(Request $request, $id)
    {
        $data = PinjamBarang::findOrFail($id);

        $data->update([
            'barang_id'     => $request->barang_id,
            'petugas_id'    => $request->petugas_id,
            'jumlah_pinjam' => $request->jumlah_pinjam,
            'bagian_id'     => $request->bagian_id,
            'tanggal_pinjam'=> $request->tanggal_pinjam,
            'keterangan'    => $request->keterangan,
            'diserahkan_ke' => $request->diserahkan_ke,
            'status'        => $request->status,
        ]);

        return redirect()->route('inventory.pinjam_barang.index')
            ->with('success', 'Data berhasil diupdate');
    }

    /* ===========================
       DELETE
    ============================*/
    public function destroy($id)
    {
        PinjamBarang::findOrFail($id)->delete();

        return redirect()->route('inventory.pinjam_barang.index')
            ->with('success', 'Data berhasil dihapus');
    }

    /* ===========================
       GENERATE KODE
    ============================*/
    private function generateKode()
    {
        $last = PinjamBarang::orderByDesc('kode')->first();
        $nomor = $last ? ((int) substr($last->kode, -4)) + 1 : 1;

        return 'P' . date('Ymd') . str_pad($nomor, 4, '0', STR_PAD_LEFT);
    }
}