<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\IoResourceController;
use App\Services\BarangService;
use App\Models\Cabang;
use Illuminate\Http\Request;

class BarangController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new BarangService();
        $this->viewPrefix = 'app.inventory.barang';
        $this->itemVariable = 'barang';

        view()->share('cabang_options', Cabang::pluck('nama','id'));

        view()->share('jenis_barang', [
            'KantongDarah' => 'Kantong Darah',
            'Buku' => 'Buku',
            'Meja' => 'Meja',
            'Kursi' => 'Kursi',
            'Kertas' => 'Kertas',
            'LainLain' => 'Lain-Lain'
        ]);
    }

    public function store(Request $request)
    {
        // VALIDASI (optional tapi disarankan)
        $request->validate([
            'kode' => 'required',
            'nama' => 'required',
            'jenis_barang' => 'required',
            'cabang_id' => 'required'
        ]);
        // dd($request);
        return parent::store($request);
    }

    public function update(Request $request, $id)
    {
        return parent::update($request,$id);
    }
}