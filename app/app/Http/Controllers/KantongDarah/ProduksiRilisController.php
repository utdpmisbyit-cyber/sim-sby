<?php

namespace App\Http\Controllers\KantongDarah;

use App\Http\Controllers\IoResourceController;
use App\Services\ProduksiRilisService;
use Illuminate\Http\Request;

class ProduksiRilisController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new ProduksiRilisService();
        $this->viewPrefix = 'app.kantong_darah.produksi_rilis';
        $this->itemVariable = 'produksi_rilis';

        // Load searchable plans dropdown options list
        $rencanaProduksis = \App\Models\RencanaProduksi::with('pengirimanSample')
            ->orderBy('tanggal', 'desc')
            ->get();

        $rencana_produksi_options = $rencanaProduksis->mapWithKeys(function ($rp) {
            $fpd = $rp->pengirimanSample->no_fpd ?? '-';
            $date = formatDate($rp->tanggal);
            return [$rp->id => "No. FPD: {$fpd} - Tanggal: {$date}"];
        })->toArray();

        view()->share('rencana_produksi_options', $rencana_produksi_options);
    }

    public function store(Request $request)
    {
        $rencana_produksi_id = $request->input('rencana_produksi_id');
        if (empty($rencana_produksi_id)) {
            return response()->json(['error' => 'Rencana Produksi wajib dipilih'], 422);
        }

        // Get all RencanaProduksiDetail for this rencana_produksi
        $details = \App\Models\RencanaProduksiDetail::where('rencana_produksi_id', $rencana_produksi_id)->get();
        if ($details->isEmpty()) {
            return response()->json(['error' => 'Rencana Produksi tidak memiliki detail kantong'], 422);
        }

        $last = \App\Models\ProduksiDarah::orderByDesc('kode')->first();
        $baseNum = $last ? (int) preg_replace('/[^0-9]/', '', $last->kode) : 0;

        $inserted = [];
        \Illuminate\Support\Facades\DB::transaction(function () use ($details, &$baseNum, &$inserted) {
            foreach ($details as $detail) {
                // If the detail does not have a jenis_darah filled, we skip it
                if (empty($detail->jenis_darah)) {
                    continue;
                }

                $barcode = $detail->no_kantong . $detail->no_satelit;
                
                // Avoid double release of same barcode
                $exists = \App\Models\ProduksiDarah::where('barcode', $barcode)->first();
                if ($exists) {
                    continue;
                }

                $baseNum++;
                $kode = 'PD' . str_pad($baseNum, 6, '0', STR_PAD_LEFT);

                $inserted[] = \App\Models\ProduksiDarah::create([
                    'kode' => $kode,
                    'barcode' => $barcode,
                    'status' => 'SENDING',
                    'pengiriman_produksi_id' => null,
                ]);
            }
        });

        if (empty($inserted)) {
            return response()->json(['error' => 'Semua kantong dari rencana produksi ini sudah dirilis sebelumnya atau belum memiliki konfigurasi Jenis Darah'], 422);
        }

        // Return first item or a success payload
        return response()->json($inserted[0] ?? ['success' => true, 'message' => 'Produksi rilis berhasil dibuat']);
    }
}
