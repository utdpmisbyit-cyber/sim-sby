<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\WilayahService;

class WilayahController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new WilayahService();
        $this->viewPrefix = 'app.master.wilayah';
        $this->itemVariable = 'wilayah';
    }
}
