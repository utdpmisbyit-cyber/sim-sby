<?php

namespace App\Services;

use App\Models\Kewarganegaraan;

class KewarganegaraanService extends IoService
{
    public function __construct()
    {
        $this->model = new Kewarganegaraan();
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
