<?php

namespace App\Services;

use App\Models\TipeKantong;

class TipeKantongService extends IoService
{
    public function __construct()
    {
        $this->model = new TipeKantong();
        $this->sort_by = ['jenis_kantong_id' => 'asc', 'nama' => 'asc'];
        $this->filters = ['parent_id'];
    }

    public function dynamic_search($model, $params = [])
    {
        $nama = $params['nama'] ?? '';
        if ($nama !== '') $model = $model->where('nama', 'like', '%' . $nama . '%');

        return $model;
    }
}
