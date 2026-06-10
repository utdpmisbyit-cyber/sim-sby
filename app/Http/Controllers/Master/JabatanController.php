<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\JabatanService;

class JabatanController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new JabatanService();
        $this->viewPrefix = 'app.master.jabatan';
        $this->itemVariable = 'jabatan';
    }
}
