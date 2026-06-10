<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\JenisKantongService;

class JenisKantongController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new JenisKantongService();
        $this->viewPrefix = 'app.master.jenis_kantong';
        $this->itemVariable = 'jenis_kantong';
    }
}
