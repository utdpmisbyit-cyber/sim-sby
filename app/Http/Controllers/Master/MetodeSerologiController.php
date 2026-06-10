<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\MetodeSerologiService;

class MetodeSerologiController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new MetodeSerologiService();
        $this->viewPrefix = 'app.master.metode_serologi';
        $this->itemVariable = 'metode_serologi';
    }
}
