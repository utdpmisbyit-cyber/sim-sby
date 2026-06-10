<?php

namespace App\Services;

use App\Models\Rekanan;

class RekananService extends IoService
{
    public function __construct()
    {
        $this->model = new Rekanan();
        $this->sort_by = ['nama_rekanan' => 'asc'];
        $this->filters = ['kode', 'kategori'];
    }

    public function dynamic_search($model, $params = [])
    {
        $nama_rekanan = $params['nama_rekanan'] ?? '';
        if ($nama_rekanan !== '') $model = $model->where('nama_rekanan', 'like', '%' . $nama_rekanan . '%');
        return $model;
    }
}
