<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\IoResourceController;
use App\Services\PermintaanSupplierService;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Barang;

class PermintaanSupplierController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new PermintaanSupplierService();
        $this->viewPrefix = 'app.inventory.permintaan_supplier';
        $this->itemVariable = 'permintaan_supplier';
    }

    // Search table
    public function search(Request $request)
    {
        $params = $request->all();
        $permintaan_supplier = $this->service->search($params);
        return view($this->viewPrefix . '._table', compact('permintaan_supplier'));
    }

    // Form tambah
    public function create()
    {
        $supplier_options = Supplier::pluck('nama', 'id');
        $barang_options = Barang::pluck('nama', 'id');

        return view($this->viewPrefix . '._form', compact('supplier_options', 'barang_options'));
    }

    // Form edit
    public function edit($id)
    {
        $permintaan_supplier = $this->service->find($id);
        $supplier_options = Supplier::pluck('nama', 'id');
        $barang_options = Barang::pluck('nama', 'id');

        return view($this->viewPrefix . '._form', compact('permintaan_supplier','supplier_options','barang_options'));
    }

    // Simpan data baru
   public function store(Request $request)
{
    $request->validate([
        'tgl_permintaan' => 'required|date',
        'supplier_id'    => 'required|exists:supplier,id',
        'barang_id'      => 'required|exists:barang,id',
        'qty'            => 'required|numeric',
        'satuan'         => 'required|string|max:20',
        'status'         => 'required|in:0,1',
        'keterangan'     => 'nullable|string|max:255',
    ]);

    // Generate no_permintaan otomatis, misal "PS-YYYYMMDD-XXXX"
    $datePart = date('Ymd');
    $last = \App\Models\PermintaanSupplier::whereDate('created_at', date('Y-m-d'))
        ->orderBy('no_permintaan', 'desc')
        ->first();

    if ($last) {
        // Ambil 4 digit terakhir dari no_permintaan terakhir
        $lastNumber = (int) substr($last->no_permintaan, -4);
        $newNumber = $lastNumber + 1;
    } else {
        $newNumber = 1;
    }

    $no_permintaan = 'PS-' . $datePart . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

    // Pastikan user_input terisi
    $request->merge([
        'no_permintaan' => $no_permintaan,
        'user_input'    => auth()->check() ? auth()->user()->id : 0
    ]);

    return parent::store($request);
}

    // Update data
    public function update(Request $request, $id)
    {
        $request->validate([
            'tgl_permintaan' => 'required|date',
            'supplier_id'    => 'required|exists:supplier,id',
            'barang_id'      => 'required|exists:barang,id',
            'qty'            => 'required|numeric',
            'satuan'         => 'required|string|max:20',
            'status'         => 'required|in:0,1',
            'keterangan'     => 'nullable|string|max:255',
        ]);

          // Update user_input juga kalau mau track siapa terakhir update
        // Bisa juga update user_input jika ingin track siapa update
    $request->merge([
        'user_input' => auth()->check() ? auth()->user()->id : 0
    ]);

        return parent::update($request, $id);
    }

    // Hapus data
    public function destroy($id)
    {
        $item = $this->service->find($id);
        if (!$item) return response()->json(['error' => 'Data tidak ditemukan']);
        $item->delete();
        return response()->json(['success' => true]);
    }

    // Restore data
    public function restore($id)
    {
        $item = $this->service->findWithTrashed($id);
        if (!$item) return response()->json(['error' => 'Data tidak ditemukan']);
        $item->restore();
        return response()->json(['success' => true]);
    }
}