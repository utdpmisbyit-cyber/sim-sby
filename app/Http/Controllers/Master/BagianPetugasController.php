<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\BagianPetugasService;

class BagianPetugasController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new BagianPetugasService();
        $this->viewPrefix = 'app.master.bagian_petugas';
        $this->itemVariable = 'bagian_petugas';
    }
}
