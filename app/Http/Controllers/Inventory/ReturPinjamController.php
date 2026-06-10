<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\IoResourceController;
use App\Models\ReturPinjam;
use App\Models\PinjamBarang;
use App\Services\PinjamBarangService;
use App\Models\Barang;
use App\Models\BagianPetugas;
use App\Models\Petugas;
use Illuminate\Http\Request;

class ReturPinjamController extends IoResourceController
{
     protected $service;
    protected $viewPrefix;
    protected $itemVariable;

    public function __construct()
    {
        $this->service      = new PinjamBarangService();
        $this->viewPrefix   = 'app.inventory.retur_pinjam';
        $this->itemVariable = 'retur_pinjam';
    }


    public function search(Request $request)
    {
        $query = ReturPinjam::with(['barang','petugas','bagianPetugas','pinjamBarang']);

        if ($request->kode) {
            $query->where('kode', 'like', "%{$request->kode}%");
        }

        $retur_pinjam = $query->orderByDesc('id')
            ->paginate($request->paginate ?? 10);

        return view("$this->viewPrefix._table", compact('retur_pinjam'));
    }

    public function create()
    {
        return view("$this->viewPrefix._form", [
            'retur_pinjam' => null,
            'barang'       => Barang::all(),
            'bagian'       => BagianPetugas::all(),
            'petugas'      => Petugas::all(),
            'pinjam'       => PinjamBarang::all(),
            'kode_otomatis' => $this->generateKode(),
        ]);
    }

    public function edit($id)
    {
        return view("$this->viewPrefix._form", [
            'retur_pinjam' => ReturPinjam::findOrFail($id),
            'barang'       => Barang::all(),
            'bagian'       => BagianPetugas::all(),
            'petugas'      => Petugas::all(),
            'pinjam'       => PinjamBarang::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pinjam_barang_id' => 'required',
            'barang_id'        => 'required',
            'petugas_id'       => 'required',
            'bagian_petugas_id'=> 'required',
            'jumlah_retur'     => 'required|numeric|min:1',
            'tanggal_retur'    => 'required|date',
        ]);

        ReturPinjam::create([
            'kode'              => $this->generateKode(),
            'pinjam_barang_id'  => $request->pinjam_barang_id,
            'barang_id'         => $request->barang_id,
            'petugas_id'        => $request->petugas_id,
            'bagian_petugas_id' => $request->bagian_petugas_id,
            'jumlah_retur'      => $request->jumlah_retur,
            'tanggal_retur'     => $request->tanggal_retur,
            'kondisi_barang'    => $request->kondisi_barang,
            'return_supplier_id'=> $request->return_supplier_id,
        ]);

        return back()->with('success', 'Data retur pinjam berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $data = ReturPinjam::findOrFail($id);

        $data->update([
            'pinjam_barang_id'  => $request->pinjam_barang_id,
            'barang_id'         => $request->barang_id,
            'petugas_id'        => $request->petugas_id,
            'bagian_petugas_id' => $request->bagian_petugas_id,
            'jumlah_retur'      => $request->jumlah_retur,
            'tanggal_retur'     => $request->tanggal_retur,
            'kondisi_barang'    => $request->kondisi_barang,
            'return_supplier_id'=> $request->return_supplier_id,
        ]);

        return back()->with('success', 'Data retur pinjam berhasil diupdate.');
    }

    public function destroy($id)
    {
        ReturPinjam::findOrFail($id)->delete();
        return back()->with('success', 'Data berhasil dihapus.');
    }

    private function generateKode()
    {
        $last = ReturPinjam::orderByDesc('id')->first();
        $nomor = $last ? $last->id + 1 : 1;
        return 'RP' . date('Ymd') . str_pad($nomor, 4, '0', STR_PAD_LEFT);
    }
}