<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\JenisPeriksaSerologiService;

class JenisPeriksaSerologiController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new JenisPeriksaSerologiService();
        $this->viewPrefix = 'app.master.jenis_periksa_serologi';
        $this->itemVariable = 'jenis_periksa_serologi';
    }
}
