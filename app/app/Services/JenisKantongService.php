<?php

namespace App\Services;

use App\Models\JenisKantong;

class JenisKantongService extends IoService
{
    public function __construct()
    {
        $this->model = new JenisKantong();
        $this->sort_by = ['nama' => 'asc'];
        $this->filters = ['parent_id'];
    }

    public function dynamic_search($model, $params = [])
    {
        $nama = $params['nama'] ?? '';
        if ($nama !== '') $model = $model->where('nama', 'like', '%' . $nama . '%');

        return $model;
    }
}
