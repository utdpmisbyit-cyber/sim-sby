<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\KelompokRumahSakitService;
use App\Services\TujuanDarahService;

class TujuanDarahController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new TujuanDarahService();
        $this->viewPrefix = 'app.master.tujuan_darah';
        $this->itemVariable = 'tujuan_darah';

        $kelompokRumahSakitService = new KelompokRumahSakitService();
        view()->share('list_kelompok_rumah_sakit', $kelompokRumahSakitService->dropdown());
    }
}
