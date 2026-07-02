<?php

namespace App\Services;

use App\Models\MobilUnit;

class MobilUnitService extends IoService
{
    public function __construct()
    {
        $this->model = new MobilUnit();
        $this->sort_by = ['merk_mobil' => 'asc'];
        $this->filters = ['kode', 'tahun_produksi', 'tahun_beli'];
    }

    public function dynamic_search($model, $params = [])
    {
        $merk_mobil = $params['merk_mobil'] ?? '';
        if ($merk_mobil !== '') $model = $model->where('merk_mobil', 'like', '%' . $merk_mobil . '%');

        $no_polisi = $params['no_polisi'] ?? '';
        if ($no_polisi !== '') $model = $model->where('no_polisi', 'like', '%' . $no_polisi . '%');

        return $model;
    }
}
