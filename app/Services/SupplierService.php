<?php

namespace App\Services;

use App\Models\Supplier;

class SupplierService extends IoService
{
    public function __construct()
    {
        $this->model = new Supplier();
        $this->sort_by = ['nama' => 'asc'];
        $this->filters = ['kode', 'status'];
    }

    public function dynamic_search($model, $params = [])
    {
        $nama = $params['nama'] ?? '';
        if ($nama !== '') $model = $model->where('nama', 'like', '%' . $nama . '%');
        return $model;
    }
}
