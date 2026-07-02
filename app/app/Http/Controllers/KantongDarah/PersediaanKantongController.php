<?php

namespace App\Http\Controllers\KantongDarah;

use App\Http\Controllers\Controller;
use App\Services\PenyimpananKantongService;
use App\Services\TipeKantongService;
use Illuminate\Http\Request;

class PersediaanKantongController extends Controller
{
    public function index()
    {
        $petugas = auth()->user()->petugas;
        $tipeKantongService = new TipeKantongService();
        $penyimpananKantongService = new PenyimpananKantongService();
        $list_tipe_kantong = $tipeKantongService->search(['with' => ['jenisKantong']]);
        foreach ($list_tipe_kantong as $tipe_kantong) {
            $tipe_kantong->stock = $penyimpananKantongService->search([
                'bagian_petugas_id' => $petugas->bagian_id,
                'tipe_kantong_id' => $tipe_kantong->id,
                'sum' => 'jumlah'
            ]);
        }

        return view('app.kantong_darah.penyimpanan_kantong.index', compact('list_tipe_kantong'));
    }
}
