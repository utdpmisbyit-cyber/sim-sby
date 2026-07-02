<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\BiayaCrossTestService;

class BiayaCrossTestController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new BiayaCrossTestService();
        $this->viewPrefix = 'app.master.biaya_cross_test';
        $this->itemVariable = 'biaya_cross_test';
    }
}
