<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\ProgramKerjaService;

class ProgramKerjaController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new ProgramKerjaService();
        $this->viewPrefix = 'app.master.program_kerja';
        $this->itemVariable = 'program_kerja';
    }
}
