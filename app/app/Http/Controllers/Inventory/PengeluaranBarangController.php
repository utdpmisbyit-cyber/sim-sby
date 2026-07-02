<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\IoResourceController;
use Illuminate\Http\Request;
use App\Services\PengeluaranBarangService;
use App\Models\PengeluaranBarang;
use App\Models\Barang;
use App\Models\BagianPetugas;
use App\Models\PengajuanBarang;
use Carbon\Carbon;
use PDF;

class PengeluaranBarangController extends IoResourceController
{
    public function __construct(PengeluaranBarangService $service)
    {
        $this->service = $service;
        $this->viewPrefix = 'app.inventory.pengeluaran_barang';
        $this->itemVariable = 'pengeluaran_barang';
    }

   
    public function create()
    {
        $barang_options = Barang::pluck('nama', 'id');
        $bagian_options = BagianPetugas::pluck('nama', 'id');

        return view($this->viewPrefix . '._form', [
            'barang_options' => $barang_options,
            'bagian_options' => $bagian_options
        ]);
    }

    // ======================
    // EDIT (FORM EDIT)
    // ======================
     public function edit($id)
    {
        $pengeluaran_barang = PengeluaranBarang::where('no_trans_keluar', $id)->firstOrFail();

        $barang_options = Barang::pluck('nama', 'id');
        $bagian_options = BagianPetugas::pluck('nama', 'id');

        return view($this->viewPrefix . '._form', compact(
            'pengeluaran_barang',
            'barang_options',
            'bagian_options'
        ));
    }

    // ======================
    // SEARCH
    // ======================
   
public function search(Request $request)
{
    $query = PengeluaranBarang::with('barang');

    if ($request->nama) {
        $query->whereHas('barang', function ($q) use ($request) {
            $q->where('nama', 'like', '%' . $request->nama . '%');
        });
    }

    if ($request->kode) {
        $query->where('no_trans_keluar', 'like', '%' . $request->kode . '%');
    }

    $pengeluaran_barang = $query->paginate(10);

    $pengajuan_pending = PengajuanBarang::with(['barang', 'petugas'])
        ->where('status', 0)
        ->orderBy('created_at', 'asc')
        ->get();

    $params = http_build_query($request->all());
    return view($this->viewPrefix . '._table', compact(
        'pengeluaran_barang',
        'pengajuan_pending',
        'params'
    ));
}
    // ======================
    // STORE
    // ======================
    public function store(Request $request)
    {
        $request->validate([
            'no_trans_keluar' => 'required',
            'tgl_keluar'      => 'required|date',
            'barang_id'       => 'required',
            'qty_keluar'      => 'required|numeric',
            'satuan'          => 'required',
        ]);

        $barang = Barang::find($request->barang_id);

        $request->merge([
            'nama_barang' => $barang->nama ?? '',
            'user_input'  => auth()->user()->name ?? 'system'
        ]);

        return parent::store($request);
    }

    // ======================
    // UPDATE
    // ======================
    public function update(Request $request, $id)
    {
        $request->validate([
            'tgl_keluar' => 'required|date',
            'barang_id'  => 'required',
            'qty_keluar' => 'required|numeric',
            'satuan'     => 'required',
        ]);

        $data = PengeluaranBarang::where('no_trans_keluar', $id)->firstOrFail();
        $barang = Barang::findOrFail($request->barang_id);

        $data->update([
            'tgl_keluar'  => $request->tgl_keluar,
            'barang_id'   => $request->barang_id,
            'qty_keluar'  => $request->qty_keluar,
            'satuan'      => $request->satuan,
            'nama_barang' => $barang->nama,
            'user_proses' => auth()->user()->name ?? 'system'
        ]);

        return response()->json([
            'message' => 'Data berhasil diupdate'
        ]);
    }


 public function laporan(Request $request)
{
    $query = PengeluaranBarang::with(['barang', 'bagian']);

    if ($request->nama) {
        $query->whereHas('barang', function ($q) use ($request) {
            $q->where('nama', 'like', "%{$request->nama}%");
        });
    }

    if ($request->kode) {
        $query->where('no_trans_keluar', 'like', "%{$request->kode}%");
    }

    if ($request->tgl_awal && $request->tgl_akhir) {
        $query->whereBetween('tgl_keluar', [
            $request->tgl_awal,
            $request->tgl_akhir
        ]);
    }

    $data = $query->orderBy('tgl_keluar')->get();
// dd($data);
    if ($request->pdf == 1) {
        return \PDF::loadView('app.inventory.pengeluaran_barang.laporan', [
            'data' => $data
        ])->setPaper('A4', 'portrait')
          ->stream('laporan_pengeluaran_barang.pdf');
    }

    return view('app.inventory.pengeluaran_barang.laporan', compact('data'));
}
}