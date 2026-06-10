<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\DiagnosaService;

class DiagnosaController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new DiagnosaService();
        $this->viewPrefix = 'app.master.diagnosa';
        $this->itemVariable = 'diagnosa';
    }
}
