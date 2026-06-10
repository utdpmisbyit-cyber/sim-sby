<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\KecamatanService;
use App\Services\WilayahService;

class KecamatanController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new KecamatanService();
        $this->viewPrefix = 'app.master.kecamatan';
        $this->itemVariable = 'kecamatan';

        $wilayahService = new WilayahService();
        view()->share('wilayah_options', $wilayahService->dropdown());
    }
}
