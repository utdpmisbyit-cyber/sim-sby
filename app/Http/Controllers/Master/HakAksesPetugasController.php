<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\HakAksesPetugasService;

class HakAksesPetugasController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new HakAksesPetugasService();
        $this->viewPrefix = 'app.master.hak_akses_petugas';
        $this->itemVariable = 'hak_akses_petugas';
    }
}
