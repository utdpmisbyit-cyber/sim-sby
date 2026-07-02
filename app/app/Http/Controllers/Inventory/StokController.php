<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\IoResourceController;
use App\Services\StokService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Stok;
use App\Models\Barang;

class StokController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new StokService();
        $this->viewPrefix = 'app.inventory.stok';
        $this->itemVariable = 'stok';
    }

    public function create()
    {
        $barang = \App\Models\Barang::pluck('nama', 'id');

        return view($this->viewPrefix . '._form', compact('barang'));
    }

    public function edit($id)
    {
        $stok = $this->service->find($id);
        $barang = \App\Models\Barang::pluck('nama', 'id');

        return view($this->viewPrefix . '._form', compact('stok', 'barang'));
    }
   public function store(Request $request)
{
    $request->validate([
        'barang_id' => 'required',
        'proses' => 'required',
        'qty_in' => 'nullable|numeric',
        'qty_out' => 'nullable|numeric',
    ]);

    return DB::transaction(function () use ($request) {

        $data = $request->all();

        // 🔥 NORMALISASI ANGKA
        $data['qty_in'] = (int) ($data['qty_in'] ?? 0);
        $data['qty_out'] = (int) ($data['qty_out'] ?? 0);

        // 🔥 AUTO NO
        $last = Stok::latest()->first();
        $kode = $last ? ((int) substr($last->no_trans_stok, -4)) + 1 : 1;

        $data['no_trans_stok'] = 'STK-' . date('Ymd') . '-' . str_pad($kode, 4, '0', STR_PAD_LEFT);

        // 🔥 FIX PROSES
        if ($data['proses'] == 1) {
            $data['qty_out'] = 0;
        } else {
            $data['qty_in'] = 0;
        }

        $stok = Stok::create($data);

        $barang = Barang::lockForUpdate()->findOrFail($data['barang_id']);

        if ($data['proses'] == 1) {
            $barang->stok += $data['qty_in'];
        } else {
            if ($barang->stok < $data['qty_out']) {
                throw new \Exception('Stok tidak cukup!');
            }
            $barang->stok -= $data['qty_out'];
        }

        $barang->save();

        return response()->json(['status' => 'success']);
    });
}
  public function update(Request $request, $id)
{
    $request->validate([
        'barang_id' => 'required',
        'proses' => 'required',
        'qty_in' => 'nullable|numeric',
        'qty_out' => 'nullable|numeric',
    ]);

    return DB::transaction(function () use ($request, $id) {

        $stok = Stok::findOrFail($id);

        // 🔥 NORMALISASI INPUT
        $data = $request->all();
        $data['qty_in'] = (int) ($data['qty_in'] ?? 0);
        $data['qty_out'] = (int) ($data['qty_out'] ?? 0);

        // ======================
        // 1. ROLLBACK LAMA
        // ======================
        $barangLama = Barang::lockForUpdate()->findOrFail($stok->barang_id);

        if ($stok->proses == 1) {
            $barangLama->stok -= $stok->qty_in;
        } else {
            $barangLama->stok += $stok->qty_out;
        }

        $barangLama->save();

        // ======================
        // 2. SET DATA BARU
        // ======================
        if ($data['proses'] == 1) {
            $data['qty_out'] = 0;
        } else {
            $data['qty_in'] = 0;
        }

        // ======================
        // 3. APPLY KE BARANG BARU
        // ======================
        $barangBaru = Barang::lockForUpdate()->findOrFail($data['barang_id']);

        if ($data['proses'] == 1) {
            $barangBaru->stok += $data['qty_in'];
        } else {
            if ($barangBaru->stok < $data['qty_out']) {
                throw new \Exception('Stok tidak cukup!');
            }
            $barangBaru->stok -= $data['qty_out'];
        }

        $barangBaru->save();

        // ======================
        // 4. UPDATE STOK
        // ======================
        $stok->update($data);

        return response()->json(['status' => 'updated']);
    });
}
}