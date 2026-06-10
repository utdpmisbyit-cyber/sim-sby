<?php

namespace App\Services;

use App\Models\Wilayah;

class WilayahService extends IoService
{
    public function __construct()
    {
        $this->model = new Wilayah();
        $this->sort_by = ['kode' => 'asc'];
        $this->filters = ['kode'];
    }

    public function dynamic_search($model, $params = [])
    {
        $nama = $params['nama'] ?? '';
        if ($nama !== '') $model = $model->where('nama', 'like', '%' . $nama . '%');
        return $model;
    }
}
