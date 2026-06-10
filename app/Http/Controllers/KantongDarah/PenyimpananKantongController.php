<?php

namespace App\Http\Controllers\KantongDarah;

use App\Http\Controllers\IoResourceController;
use App\Services\PenyimpananKantongService;

class PenyimpananKantongController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new PenyimpananKantongService();
        $this->viewPrefix = 'app.kantong_darah.penyimpanan_kantong';
        $this->itemVariable = 'penyimpanan_kantong';
    }

}
