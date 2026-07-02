<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\KelompokRumahSakitService;

class KelompokRumahSakitController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new KelompokRumahSakitService();
        $this->viewPrefix = 'app.master.kelompok_rumah_sakit';
        $this->itemVariable = 'kelompok_rumah_sakit';
    }
}
