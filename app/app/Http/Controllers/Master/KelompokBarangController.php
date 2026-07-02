<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\KelompokBarangService;

class KelompokBarangController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new KelompokBarangService();
        $this->viewPrefix = 'app.master.kelompok_barang';
        $this->itemVariable = 'kelompok_barang';
    }
}
