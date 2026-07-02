<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\ReagenSerologiService;

class ReagenSerologiController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new ReagenSerologiService();
        $this->viewPrefix = 'app.master.reagen_serologi';
        $this->itemVariable = 'reagen_serologi';
    }
}
