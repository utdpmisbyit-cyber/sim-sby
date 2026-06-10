<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\JenisDarahService;

class JenisDarahController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new JenisDarahService();
        $this->viewPrefix = 'app.master.jenis_darah';
        $this->itemVariable = 'jenis_darah';
    }
}
