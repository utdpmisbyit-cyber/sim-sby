<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\JenisBiayaService;

class JenisBiayaController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new JenisBiayaService();
        $this->viewPrefix = 'app.master.jenis_biaya';
        $this->itemVariable = 'jenis_biaya';
    }
}
