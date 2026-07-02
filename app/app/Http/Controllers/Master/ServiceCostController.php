<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\JenisBiayaService;
use App\Services\KelompokBiayaService;
use App\Services\ServiceCostService;

class ServiceCostController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new ServiceCostService();
        $this->viewPrefix = 'app.master.service_cost';
        $this->itemVariable = 'service_cost';

        $list_jenis = $this->service->jenis;
        view()->share('list_jenis', $list_jenis);
        $jenisBiayaService = new JenisBiayaService();
        view()->share('list_jenis_biaya', $jenisBiayaService->dropdown());
        $kelompokBiayaService = new KelompokBiayaService();
        view()->share('list_kelompok_biaya', $kelompokBiayaService->dropdown());
    }
}
