<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\IoResourceController;
use App\Models\Barang;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Anggaran;
use App\Models\Supplier;
use App\Services\PurchaseOrderService;
use App\Services\PurchaseOrderDetailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

class PurchaseOrderController extends IoResourceController
{
    protected PurchaseOrderDetailService $detailService;

    public function __construct()
    {
        $this->service       = new PurchaseOrderService();
        $this->detailService = new PurchaseOrderDetailService();
        $this->viewPrefix    = 'app.inventory.purchase_order';
        $this->itemVariable  = 'purchase_order';
    }

    // ─── Helper: data untuk form ─────────────────────────────────────────────
    private function getFormOptions(): array
    {
        $supplier_options = Supplier::orderBy('nama')->pluck('nama', 'id')->toArray();
        $barang_list      = Barang::orderBy('kode')->get();

        return compact('supplier_options', 'barang_list');
    }

    // ─── Create (modal form kosong) ──────────────────────────────────────────
    public function create()
    {
        return view("{$this->viewPrefix}._form", $this->getFormOptions());
    }

    // ─── Edit (modal form isi data) ──────────────────────────────────────────
    public function edit($id)
    {
        $purchase_order = $this->service->find($id);

        return view("{$this->viewPrefix}._form", array_merge(
            $this->getFormOptions(),
            ['purchase_order' => $purchase_order]
        ));
    }

    // ─── Search / tabel ──────────────────────────────────────────────────────
    public function search(Request $request)
    {
        $items = $this->service->search($request->all());

        return view("{$this->viewPrefix}._table", [
            'purchase_order' => $items,
        ]);
    }

    // ─── Store ───────────────────────────────────────────────────────────────
   public function store(Request $request)
{
    $request->validate([
        'no_po'                 => 'required',
        'tgl_po'                => 'required|date',
        'supplier_id'           => 'required',
        'details'               => 'required|array|min:1',
        'details.*.barang_id'   => 'required',
        'details.*.qty_po'      => 'required|numeric|min:1',
        'details.*.harga_po'    => 'required|numeric|min:0',
        'details.*.subtotal_po' => 'required|numeric|min:0',
    ]);

    DB::beginTransaction();
    try {
        $total = collect($request->details)
            ->sum(fn($d) => $d['subtotal_po'] ?? 0);

        // 
        $anggaran = Anggaran::create([
            'kode'            => 'ANG-' . time(),
            'tgl_input'       => now(),
            'tahun_anggaran'  => date('Y'),
            'keterangan'      => 'Auto dari PO ' . $request->no_po,
            'nilai_anggaran'  => $total,
            'user_input'      => auth()->id() ?? 1,
        ]);

        // 
        $po = PurchaseOrder::create([
            'no_po'       => $request->no_po,
            'tgl_po'      => $request->tgl_po,
            'supplier_id' => $request->supplier_id,
            'total_po'    => $total,

            'status_po'   => 0,
            'app_po'      => 0,
            'user_input'  => auth()->id() ?? 1,

            'barang_id' => collect($request->details)->first()['barang_id'] ?? null,
            'anggaran_id' => $anggaran->id,
        ]);
        // dd($po->id);
        
        foreach ($request->details as $det) {
            $po->details()->create([
                'purchase_order_id' => $po->id,
                'barang_id'   => $det['barang_id'],
                'qty_po'      => $det['qty_po'],
                'harga_po'    => $det['harga_po'],
                'subtotal_po' => $det['subtotal_po'],
            ]);
        }

        DB::commit();

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

    // ─── Update ──────────────────────────────────────────────────────────────
 public function update(Request $request, $id)
{
    try {
        DB::beginTransaction();

        // 🔥 ambil PO pakai ID (bukan no_po)
        $po = PurchaseOrder::findOrFail($id);

        // 🔥 update header
        $po->update([
            'tgl_po'      => $request->tgl_po,
            'supplier_id' => $request->supplier_id,
            'status_po'   => $request->status_po,
            'total_po'    => $request->total_po,
        ]);

        // 🔥 hapus detail lama
        PurchaseOrderDetail::where('purchase_order_id', $po->id)->delete();

        $total = 0;

        // 🔥 insert ulang detail
        foreach ($request->details as $det) {

            $subtotal = $det['qty_po'] * $det['harga_po'];

            PurchaseOrderDetail::create([
                'purchase_order_id' => $po->id,
                'barang_id'         => $det['barang_id'],
                'qty_po'            => $det['qty_po'],
                'harga_po'          => $det['harga_po'], // 🔥 INI YANG PENTING
                'subtotal_po'       => $subtotal,
            ]);

            $total += $subtotal;
        }

        // 🔥 update total lagi biar sinkron
        $po->update([
            'total_po' => $total
        ]);

        // 🔥 update anggaran (kalau ada)
        if ($po->anggaran_id) {
            DB::table('anggaran')
                ->where('id', $po->anggaran_id)
                ->update([
                    'nilai_anggaran' => $total
                ]);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diupdate'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
}