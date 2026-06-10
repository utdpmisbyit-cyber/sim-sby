<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\PekerjaanService;

class PekerjaanController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new PekerjaanService();
        $this->viewPrefix = 'app.master.pekerjaan';
        $this->itemVariable = 'pekerjaan';
    }
}
