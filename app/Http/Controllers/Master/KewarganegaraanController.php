<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\KewarganegaraanService;

class KewarganegaraanController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new KewarganegaraanService();
        $this->viewPrefix = 'app.master.kewarganegaraan';
        $this->itemVariable = 'kewarganegaraan';
    }
}
