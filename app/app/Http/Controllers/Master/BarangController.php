<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\BarangService;
use App\Services\CabangService;

class BarangController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new BarangService();
        $this->viewPrefix = 'app.master.barang';
        $this->itemVariable = 'barang';

        view()->share('list_jenis', $this->service->jenis_barang);
        $cabangService = new CabangService();
        view()->share('list_cabang', $cabangService->dropdown());
    }
}
