<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\BagianRumahSakitService;

class BagianRumahSakitController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new BagianRumahSakitService();
        $this->viewPrefix = 'app.master.bagian_rumah_sakit';
        $this->itemVariable = 'bagian_rumah_sakit';
    }
}
