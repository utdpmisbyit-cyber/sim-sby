<?php

namespace App\Services;

use App\Models\OpnameBarang;

class OpnameBarangService extends IoService
{
    public function __construct()
    {
        $this->model = new OpnameBarang();
        $this->sort_by = ['tgl_opname' => 'desc'];
        $this->filters = ['status', 'petugas_id', 'barang_id', 'user_input'];
    }

    public function dynamic_search($model, $params = [])
    {
        $no_opname = $params['no_opname'] ?? '';
        if ($no_opname !== '') $model = $model->where('no_opname', 'like', '%' . $no_opname . '%');
        return $model;
    }
}
