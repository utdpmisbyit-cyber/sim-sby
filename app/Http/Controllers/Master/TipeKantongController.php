<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\JenisKantongService;
use App\Services\TipeKantongService;

class TipeKantongController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new TipeKantongService();
        $this->viewPrefix = 'app.master.tipe_kantong';
        $this->itemVariable = 'tipe_kantong';

        $jenisKantongService = new JenisKantongService();
        view()->share('jenis_kantong_options', $jenisKantongService->dropdown());
    }
}
