<?php

namespace App\Services;

use App\Models\Pekerjaan;

class PekerjaanService extends IoService
{
    public function __construct()
    {
        $this->model = new Pekerjaan();
        $this->sort_by = ['nama' => 'asc'];
        $this->filters = ['kode'];
    }

    public function dynamic_search($model, $params = [])
    {
        $nama = $params['nama'] ?? '';
        if ($nama !== '') $model = $model->where('nama', 'like', '%' . $nama . '%');
        return $model;
    }
}
