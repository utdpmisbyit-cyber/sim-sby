<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\KelompokBiayaService;

class KelompokBiayaController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new KelompokBiayaService();
        $this->viewPrefix = 'app.master.kelompok_biaya';
        $this->itemVariable = 'kelompok_biaya';
    }
}
