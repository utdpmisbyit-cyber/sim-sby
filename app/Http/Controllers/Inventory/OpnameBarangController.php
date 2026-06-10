<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\IoResourceController;
use App\Models\OpnameBarang;
use App\Models\Barang;
use App\Models\Petugas;
use Illuminate\Http\Request;
use App\Models\BagianPetugas;

class OpnameBarangController extends IoResourceController
{
    protected $service;
    protected $viewPrefix;
    protected $itemVariable;

    public function __construct()
    {
        $this->viewPrefix   = 'app.inventory.opname_barang';
        $this->itemVariable = 'opname_barang';
    }

    public function search(Request $request)
    {
        $query = OpnameBarang::with(['barang','petugas']);

        if ($request->no_opname) {
            $query->where('no_opname', 'like', "%{$request->no_opname}%");
        }

        if ($request->barang_id) {
            $query->where('barang_id', $request->barang_id);
        }

        $opname_barang = $query->orderByDesc('tgl_opname')
            ->paginate($request->paginate ?? 10);

        return view("$this->viewPrefix._table", compact('opname_barang'));
    }

    public function create()
    {
        return view("$this->viewPrefix._form", [
            'opname_barang' => null,
            'barang'        => Barang::with(['stok'])->get(),
            'bagian'        => BagianPetugas::all(),
            'petugas'       => Petugas::all(),
            'kode_otomatis' => $this->generateKode(),
        ]);
    }

    public function edit($id)
    {
        return view("$this->viewPrefix._form", [
            'opname_barang' => OpnameBarang::where('no_opname',$id)->firstOrFail(),
            'barang'        => Barang::with(['stok'])->get(),
            'petugas'       => Petugas::all(),
            'bagian'        => BagianPetugas::all(),
            'kode_otomatis' => $id,
        ]);
    }   
    public function stok() {
        return $this->hasOne(Stok::class, 'barang_id');
    }

    public function getStokAkhirAttribute()
    {
        return $this->stok->akhir ?? 0;
    }

     public function store(Request $request)
    {
        $request->validate([
            'tgl_opname'  => 'required|date',
            'barang_id'   => 'required|integer',
            'qty_fisik'   => 'required|numeric',
            'petugas_id'  => 'required|integer',
            'lokasi'      => 'required|string',
            'satuan'      => 'required|string',
            'keterangan'  => 'nullable|string',
            'status'      => 'nullable|string',
        ]);

        $barang = Barang::with('stok')->findOrFail($request->barang_id);
// dd($barang->stok);
       
        // Hitung stok akhir
        $qty_in  = $barang->stok->sum('qty_in');
        $qty_out = $barang->stok->sum('qty_out');
        $qty_sistem = $qty_in - $qty_out;
        OpnameBarang::create([
            'no_opname'   => $this->generateKode(),
            'tgl_opname'  => $request->tgl_opname,
            'barang_id'   => $request->barang_id,
            'nama_barang' => $barang->nama,
            'qty_sistem'  => $qty_sistem,
            'qty_fisik'   => $request->qty_fisik,
            'selisih'     => $request->qty_fisik - $qty_sistem,
            'petugas_id'  => $request->petugas_id,
            'satuan'      => $barang->satuan,
            'lokasi'      => $request->lokasi,
            'keterangan'  => $request->keterangan ?? '-',
            'status'      => $request->status ?? 'opname selesai',
            'user_input'  => Petugas::find($request->petugas_id)->nama ?? '-',
        ]);

        return back()->with('success', 'Data opname berhasil ditambahkan.');
    }

    // =====================
    //      UPDATE
    // =====================
    public function update(Request $request, $id)
    {
        // dd($request)->all();
        $data = OpnameBarang::where('no_opname',$id)->firstOrFail();
        $barang = Barang::with('stok')->findOrFail($request->barang_id);

        $qty_in  = $barang->stok->sum('qty_in');
        $qty_out = $barang->stok->sum('qty_out');
        $qty_sistem = $qty_in - $qty_out;

        $data->update([
            'tgl_opname'  => $request->tgl_opname,
            'barang_id'   => $request->barang_id,
            'nama_barang' => $barang->nama,
            'qty_sistem'  => $qty_sistem,
            'qty_fisik'   => $request->qty_fisik,
            'selisih'     => $request->qty_fisik - $qty_sistem,
            'petugas_id'  => $request->petugas_id,
            'satuan'      => $barang->satuan,
            'lokasi'      => $request->lokasi,
            'keterangan'  => $request->keterangan ?? '-',
            'status'      => $request->status ?? 'opname selesai',
            'user_proses' => Petugas::find($request->petugas_id)->nama ?? '-',
        ]);

        return back()->with('success', 'Data opname berhasil diupdate.');
    }


    public function destroy($id)
    {
        OpnameBarang::where('no_opname', $id)->firstOrFail()->delete();
        return back()->with('success', 'Data berhasil dihapus.');
    }

    private function generateKode()
    {
        $last = OpnameBarang::orderByDesc('no_opname')->first();
        $num  = $last ? intval(substr($last->no_opname, -4)) + 1 : 1;

        return 'OPN' . date('Ymd') . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}