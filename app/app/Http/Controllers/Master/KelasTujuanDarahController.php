<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\KelasTujuanDarahService;

class KelasTujuanDarahController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new KelasTujuanDarahService();
        $this->viewPrefix = 'app.master.kelas_tujuan_darah';
        $this->itemVariable = 'kelas_tujuan_darah';
    }
}
