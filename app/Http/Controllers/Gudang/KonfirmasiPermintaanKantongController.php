<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\IoResourceController;
use App\Services\JenisKantongService;
use App\Services\PenyimpananKantongService;
use App\Services\PermintaanKantongService;
use App\Services\PetugasService;
use App\Services\TipeKantongService;
use Illuminate\Http\Request;

class KonfirmasiPermintaanKantongController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new PermintaanKantongService();
        $this->viewPrefix = 'app.gudang.konfirmasi_permintaan';
        $this->itemVariable = 'permintaan_kantong';
    }
    


}