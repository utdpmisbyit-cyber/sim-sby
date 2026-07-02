<?php

namespace App\Services;

use App\Models\QcDetailLot;

class QcDetailLotService extends IoService
{
    public function __construct()
    {
        $this->model = new QcDetailLot();
        $this->sort_by = ['id' => 'asc'];
        $this->filters = ['qc_barang_masuk_id', 'barang_id', 'jenis_barang'];
    }

    public function dynamic_search($model, $params = [])
    {
        $no_lot = $params['no_lot'] ?? '';
        if ($no_lot !== '') $model = $model->where('no_lot', 'like', '%' . $no_lot . '%');
        return $model;
    }
}
