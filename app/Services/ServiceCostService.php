<?php

namespace App\Services;

use App\Models\ServiceCost;

class ServiceCostService extends IoService
{
    public array $jenis = ServiceCost::JENIS;

    public function __construct()
    {
        $this->model = new ServiceCost();
        $this->sort_by = ['id' => 'asc'];
        $this->filters = ['kode', 'jenis', 'jenis_biaya_id', 'kelompok_biaya_id'];
    }

    public function filter_params($params, $id = '')
    {
        return $this->cleanNumber($params, ['biaya']);
    }
}
