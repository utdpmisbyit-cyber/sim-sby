<?php

namespace App\Services;

use App\Models\Kecamatan;

class KecamatanService extends IoService
{
    public function __construct()
    {
        $this->model = new Kecamatan();
        $this->sort_by = ['kode' => 'asc'];
        $this->filters = ['kode', 'wilayah_id'];
    }

    public function dynamic_search($model, $params = [])
    {
        $nama = $params['nama'] ?? '';
        if ($nama !== '') $model = $model->where('nama', 'like', '%' . $nama . '%');
        return $model;
    }
}
