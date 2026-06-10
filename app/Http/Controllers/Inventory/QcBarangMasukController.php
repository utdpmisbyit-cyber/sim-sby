<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\IoResourceController;
use App\Services\QcBarangMasukService;
use App\Services\QcDetailLotService;
use App\Models\QcBarangMasuk;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;

class QcBarangMasukController extends IoResourceController
{
    protected $service;
    protected $viewPrefix;
    protected $itemVariable;
    protected $detailService;

    public function __construct()
    {
        $this->service       = new QcBarangMasukService();
        $this->detailService = new QcDetailLotService();
        $this->viewPrefix    = 'app.inventory.QC_barang_masuk';
        $this->itemVariable  = 'pembelian_qc_masuk';
        
    }
    public function search(Request $request)
    {
        $query = QcBarangMasuk::with(['qcDetailLot.barang']);

        if ($request->nama) {
            $query->where('no_trans_qc', 'like', '%' . $request->nama . '%');
        }

        if ($request->kode) {
            $query->where('no_faktur', 'like', '%' . $request->kode . '%');
        }

        $pembelian_qc_masuk = $query
            ->orderByDesc('id')
            ->paginate($request->paginate ?? 10);

        return view($this->viewPrefix . '._table', compact('pembelian_qc_masuk'));
    }
    // Override create untuk kirim next_no ke view
    public function create()
    {
        $next_no = $this->generate_no_trans();
        $purchase_orders = PurchaseOrder::pluck('no_po','id'); // id => no_po
        return view($this->viewPrefix.'._form', compact('next_no','purchase_orders'));
    }

    // Override edit untuk load relasi detail lot
    public function edit($id)
    {
        $pembelian_qc_masuk = QcBarangMasuk::with('qcDetailLot.barang')->findOrFail($id);
        $next_no = $pembelian_qc_masuk->no_trans_qc;

         $purchase_orders = PurchaseOrder::pluck('no_po','id'); // WAJIB

        return view($this->viewPrefix . '._form', compact('pembelian_qc_masuk', 'next_no', 'purchase_orders'));
    }
    public function show_json($id)
    {
        
        $po = PurchaseOrder::with(['details.barang', 'supplier'])->findOrFail($id);

        return response()->json([
            'supplier_id' => $po->supplier_id,
            'supplier_kode' => $po->supplier->kode ?? '',
            'detail' => $po->details->map(function ($d) {
                return [
                    'barang_id' => $d->barang_id,
                    'qty_po'    => $d->qty_po,
                    'harga_po'  => $d->harga_po,
                    'barang'    => [
                        'nama_barang'  => $d->barang->nama ?? '-',
                        'jenis_barang' => $d->barang->jenis_barang ?? '-',
                    ],
                ];
            }),
        ]);
    }

public function store(Request $request)
{
    DB::beginTransaction();

    try {

        $request->validate([
            'tgl_qc' => 'required|date',
            'tgl_beli' => 'required|date',
            'status_qc' => 'required',
            'no_faktur' => 'required',
            'purchase_order_id' => 'required',
            'supplier_id' => 'required',
        ]);

        $request->merge([
            'no_trans_qc' => $this->generate_no_trans(),
            'user_proses' => auth()->user()->name ?? 'Admin',
        ]);

        $qc = QcBarangMasuk::create($request->except('detail'));

        foreach ($request->detail ?? [] as $row) {
            if (empty($row['barang_id'])) continue;

            $qc->qcDetailLot()->create([
                
                'barang_id'      => $row['barang_id'],
                'no_lot'         => $row['no_lot'] ?? null,
                'jenis_barang'   => $row['jenis_barang'] ?? null,
                'qty_terima'     => $row['qty_terima'] ?? 0,
                'harga'          => $row['harga'] ?? 0,
                'subtotal_harga' => ($row['qty_terima'] ?? 0) * ($row['harga'] ?? 0),
                'tgl_exp_date'   => $row['tgl_exp_date'] ?? null,
                'suhu'           => $row['suhu'] ?? 0,
            ]);
        }

        DB::commit();

        return response()->json(['success' => true]);

    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json([
            'message' => $e->getMessage()
        ], 500);
    }
}

    public function update(Request $request, $id)
    {
        $request->validate([
            'tgl_qc'            => 'required|date',
            'tgl_beli'          => 'required|date',
            'status_qc'         => 'required',
            'no_faktur'         => 'required',
            'purchase_order_id' => 'required',
            'supplier_id'       => 'required',
        ]);

        $qc = QcBarangMasuk::findOrFail($id);
        $qc->update($request->except(['detail', '_token', '_method']));

        // Sync detail lot: hapus lama, insert baru
        $qc->qcDetailLot()->delete();
        if ($request->has('detail')) {
            foreach ($request->detail as $row) {
                if (empty($row['barang_id'])) continue;
                $qc->qcDetailLot()->create([
                
                    'barang_id'      => $row['barang_id'],
                    'no_lot'         => $row['no_lot'] ?? null,
                    'jenis_barang'   => $row['jenis_barang'] ?? null,
                    'qty_terima'     => $row['qty_terima'] ?? 0,
                    'harga'          => $row['harga'] ?? 0,
                    'subtotal_harga' => ($row['qty_terima'] ?? 0) * ($row['harga'] ?? 0),
                    'tgl_exp_date'   => $row['tgl_exp_date'] ?? null,
                    'suhu'           => $row['suhu'] ?? 0,
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    // Auto generate: QC-000001, QC-000002, dst
    private function generate_no_trans(): string
    {
        $last = QcBarangMasuk::withTrashed()->orderByDesc('no_trans_qc')->first();
        $num  = $last ? ((int) substr($last->no_trans_qc, 3)) + 1 : 1;
        return 'QC-' . str_pad($num, 6, '0', STR_PAD_LEFT);
    }
}