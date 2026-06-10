<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\MobilUnitService;

class MobilUnitController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new MobilUnitService();
        $this->viewPrefix = 'app.master.mobil_unit';
        $this->itemVariable = 'mobil_unit';
    }
}
