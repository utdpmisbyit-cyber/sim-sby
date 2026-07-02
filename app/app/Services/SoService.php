<?php

namespace App\Services;

use App\Models\So;

class SoService extends IoService
{
    public function __construct()
    {
        $this->model = new So();
        $this->sort_by = ['tgl_so' => 'desc'];
        $this->filters = ['barang_id', 'qc_barang_masuk_id'];
    }

    public function dynamic_search($model, $params = [])
    {
        $no_trans_so = $params['no_trans_so'] ?? '';
        if ($no_trans_so !== '') $model = $model->where('no_trans_so', 'like', '%' . $no_trans_so . '%');
        return $model;
    }
}
