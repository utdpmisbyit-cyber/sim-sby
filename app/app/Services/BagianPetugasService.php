<?php

namespace App\Services;

use App\Models\BagianPetugas;

class BagianPetugasService extends IoService
{
    public function __construct()
    {
        $this->model = new BagianPetugas();
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
