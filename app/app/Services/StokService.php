<?php

namespace App\Services;

use App\Models\Stok;

class StokService extends IoService
{
    public function __construct()
    {
        $this->model = new Stok();
        $this->sort_by = ['tgl_proses' => 'desc'];
        $this->filters = ['barang_id', 'proses', 'aktif'];
    }

    public function dynamic_search($model, $params = [])
    {
        $no_trans_stok = $params['no_trans_stok'] ?? '';
        if ($no_trans_stok !== '') $model = $model->where('no_trans_stok', 'like', '%' . $no_trans_stok . '%');
        return $model;
    }
}
