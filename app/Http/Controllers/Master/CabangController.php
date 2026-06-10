<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\CabangService;

class CabangController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new CabangService();
        $this->viewPrefix = 'app.master.cabang';
        $this->itemVariable = 'cabang';

        view()->share('jenis_options', $this->service->list_jenis);
    }
}
