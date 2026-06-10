<?php

namespace App\Services;

use App\Models\ReturnSupplier;

class ReturnSupplierService extends IoService
{
    public function __construct()
    {
        $this->model = new ReturnSupplier();
        $this->sort_by = ['tgl_retur' => 'desc'];
        $this->filters = ['supplier_id', 'jenis_retur', 'user_input'];
    }

    public function dynamic_search($model, $params = [])
    {
        $no_trans_retur = $params['no_trans_retur'] ?? '';
        if ($no_trans_retur !== '') $model = $model->where('no_trans_retur', 'like', '%' . $no_trans_retur . '%');
        return $model;
    }
    public function query()
    {
        return $this->model->newQuery();
    }
}
