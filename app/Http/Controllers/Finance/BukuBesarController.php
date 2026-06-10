<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\IoResourceController;
use App\Models\TrialBalance;
use Illuminate\Http\Request;

class BukuBesarController extends IoResourceController
{
    protected $viewPrefix = 'app.finance.buku_besar';
    protected $itemVariable = 'buku_besar';

    // ======================
    // INDEX (NO DATA)
    // ======================
    public function index()
    {
        return view("$this->viewPrefix.index");
    }

    // ======================
    // SEARCH (FILTER + SEARCH)
    // ======================
    public function search(Request $request)
    {
        $data = TrialBalance::with('coa')
            ->select([
                'id', 'kode', 'coa_id', 'nama_akun', 'sa_debet', 'sa_kredit',
                'debet', 'kredit', 'neraca_debet', 'neraca_kredit',
                'kategori1', 'kategori2', 'pos_saldo', 'pos_laporan', 'created_at'
            ])
            ->orderBy('kode');

        // ============================
        // FILTER COA (Fix: gunakan ID)
        // ============================
        if ($request->filter_coa) {
            $data->where('coa_id', $request->filter_coa);
        }

        // SEARCH KODE
        if ($request->search_kode) {
            $data->where('kode', 'like', "%{$request->search_kode}%");
        }

        // SEARCH NAMA AKUN
        if ($request->search_nama) {
            $data->where('nama_akun', 'like', "%{$request->search_nama}%");
        }

        $buku_besar = $data->get();

        return view("$this->viewPrefix._table", compact('buku_besar'));
    }
}