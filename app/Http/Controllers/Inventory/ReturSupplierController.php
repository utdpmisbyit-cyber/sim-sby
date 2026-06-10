<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\IoResourceController;
use App\Services\ReturnSupplierService;
use Illuminate\Http\Request;
use App\Models\ReturnSupplier;
use App\Models\ReturnSupplierDetail;
use App\Models\Supplier;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;


class ReturSupplierController extends IoResourceController
{
    public function __construct()
    {
        $this->model = new ReturnSupplier();
        $this->service = new ReturnSupplierService();
        $this->viewPrefix = 'app.inventory.return_supplier';
        $this->itemVariable = 'return_supplier';
    }

    public function store(Request $request)
{
    $request->validate([
        'tgl_retur'   => 'required|date',
        'supplier_id' => 'required',
        'jenis_retur' => 'required',

        // detail
        'barang_id'     => 'required|array',
        'qty_retur'     => 'required|array',
        'harga_retur'   => 'required|array',
    ]);

    DB::beginTransaction();
    try {

        // ✅ pakai dari form (JANGAN generate lagi)
        $no = $request->no_trans_retur;

        // ==================
        // SIMPAN HEADER
        // ==================
        $header = ReturnSupplier::create([
            'no_trans_retur' => $no,
            'tgl_retur'      => $request->tgl_retur,
            'supplier_id'    => $request->supplier_id,
            'jenis_retur'    => $request->jenis_retur,
            'satuan'         => $request->satuan,
            'user_input'     => auth()->id() ?? 0,
            'jml_retur'      => 0,
            'total_retur'    => 0,
        ]);

        // ==================
        // SIMPAN DETAIL
        // ==================
        $total = 0;
        $jml   = 0;
        $id_header = $header->id ?? ReturnSupplier::latest()->first()->id;

        foreach ($request->barang_id as $i => $barang) {

            $qty   = $request->qty_retur[$i] ?? 0;
            $harga = $request->harga_retur[$i] ?? 0;
            $sub   = $qty * $harga;

            ReturnSupplierDetail::create([
                // ✅ FIX DI SINI (WAJIB)
                'return_supplier_id' => $id_header,

                'barang_id'          => $barang,
                'qty_retur'          => $qty,
                'harga_retur'        => $harga,
                'subtotal_retur'     => $sub,
            ]);

            $total += $sub;
            $jml   += $qty;
        }

        // ==================
        // UPDATE TOTAL HEADER
        // ==================
        $header->update([
            'jml_retur'   => $jml,
            'total_retur' => $total
        ]);

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil disimpan'
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ]);
    }
}

    public function update(Request $request, $id)
{
    $request->validate([
        'tgl_retur'   => 'required|date',
        'supplier_id' => 'required',
        'jenis_retur' => 'required',

        'barang_id'   => 'required|array',
        'qty_retur'   => 'required|array',
        'harga_retur' => 'required|array',
    ]);

    DB::beginTransaction();
    try {

        $header = ReturnSupplier::findOrFail($id);

        // ==================
        // UPDATE HEADER
        // ==================
        $header->update([
            'tgl_retur'   => $request->tgl_retur,
            'supplier_id' => $request->supplier_id,
            'jenis_retur' => $request->jenis_retur,
            'satuan'      => $request->satuan,
        ]);

        // ==================
        // HAPUS DETAIL LAMA
        // ==================
        ReturnSupplierDetail::where('return_supplier_id', $id)->delete();

        // ==================
        // INSERT DETAIL BARU
        // ==================
        $total = 0;
        $jml   = 0;

        foreach ($request->barang_id as $i => $barang) {

            $qty   = $request->qty_retur[$i] ?? 0;
            $harga = $request->harga_retur[$i] ?? 0;
            $sub   = $qty * $harga;

            ReturnSupplierDetail::create([
                'return_supplier_id' => $id,
                'barang_id'          => $barang,
                'qty_retur'          => $qty,
                'harga_retur'        => $harga,
                'subtotal_retur'     => $sub,
            ]);

            $total += $sub;
            $jml   += $qty;
        }

        // ==================
        // UPDATE TOTAL
        // ==================
        $header->update([
            'jml_retur'   => $jml,
            'total_retur' => $total
        ]);

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil diupdate'
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ]);
    }
}

    public function search(Request $request)
    {
          $params = $request->all();

        $model = $this->service->query(); // ✅ BENAR

        $model = $this->service->dynamic_search($model, $params);

        $return_supplier = $model->paginate(10);

        return view($this->viewPrefix . '._table', compact('return_supplier'));
    }
    public function create()
{
    $supplier_options = Supplier::pluck('nama', 'id');
    $barang_options   = Barang::pluck('nama', 'id');

    // AUTO NO RETUR
    $last = ReturnSupplier::orderBy('id','desc')->first();
    $next = $last ? $last->id + 1 : 1;
    $no_retur = 'RET-' . str_pad($next, 5, '0', STR_PAD_LEFT);

    return view($this->viewPrefix . '._form', compact(
        'supplier_options',
        'barang_options',
        'no_retur'
    ));
}

 public function edit($id)
{
    $return_supplier = $this->service->find($id);

    $supplier_options = Supplier::pluck('nama', 'id');
    $barang_options   = Barang::pluck('nama', 'id');

    // 🔥 ambil detail
   $details = ReturnSupplierDetail::where('return_supplier_id', $id)->get();
   
    return view($this->viewPrefix . '._form', compact(
        'return_supplier',
        'supplier_options',
        'barang_options',
        'details'
    ));
}


}