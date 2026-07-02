<?php

namespace App\Services;

use App\Models\PermintaanSupplier;

class PermintaanSupplierService extends IoService
{
    public function __construct()
    {
        $this->model = new PermintaanSupplier();
        $this->sort_by = ['tgl_permintaan' => 'desc'];
        $this->filters = ['supplier_id', 'barang_id', 'status', 'user_input'];
    }

    public function dynamic_search($model, $params = [])
    {
        $no_permintaan = $params['no_permintaan'] ?? '';
        if ($no_permintaan !== '') $model = $model->where('no_permintaan', 'like', '%' . $no_permintaan . '%');
        return $model;
    }
}
