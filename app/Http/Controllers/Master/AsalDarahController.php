<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\AsalDarahService;

class AsalDarahController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new AsalDarahService();
        $this->viewPrefix = 'app.master.asal_darah';
        $this->itemVariable = 'asal_darah';
    }
}
