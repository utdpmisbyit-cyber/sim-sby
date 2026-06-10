<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PendataanKantong;

class CetakUlangBarcodeController extends Controller
{
    public function index()
    {
        return view('app.gudang.cetak_ulang_barcode.index');
    }

    public function data(Request $request)
    {
        $query = PendataanKantong::query();

        if ($request->filled('barcode')) {
            $query->where('barcode', 'like', '%' . $request->barcode . '%');
        }

        if ($request->filled('no_lot')) {
            $query->where('no_lot', 'like', '%' . $request->no_lot . '%');
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        $rows = $query
            ->latest()
            ->limit(500)
            ->get();

        return response()->json($rows);
    }
}