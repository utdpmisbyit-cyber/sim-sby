<?php

namespace App\Services;

use App\Models\KelompokRumahSakit;

class KelompokRumahSakitService extends IoService
{
    public function __construct()
    {
        $this->model = new KelompokRumahSakit();
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
