<?php

namespace App\Services;

use App\Models\QcBarangMasuk;

class QcBarangMasukService extends IoService
{
    public function __construct()
    {
        $this->model = new QcBarangMasuk();
        $this->sort_by = ['tgl_qc' => 'desc'];
        $this->filters = ['status_qc', 'purchase_order_id', 'supplier_id', 'user_proses'];
    }

    public function dynamic_search($model, $params = [])
    {
        $no_trans_qc = $params['no_trans_qc'] ?? '';
        if ($no_trans_qc !== '') $model = $model->where('no_trans_qc', 'like', '%' . $no_trans_qc . '%');

        $no_faktur = $params['no_faktur'] ?? '';
        if ($no_faktur !== '') $model = $model->where('no_faktur', 'like', '%' . $no_faktur . '%');

        return $model;
    }
}
