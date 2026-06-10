<?php

namespace App\Services;

use App\Models\TujuanDarah;

class TujuanDarahService extends IoService
{
    public function __construct()
    {
        $this->model = new TujuanDarah();
        $this->sort_by = ['nama' => 'asc'];
        $this->filters = ['kode', 'kelompok_rumah_sakit_id'];
    }

    public function dynamic_search($model, $params = [])
    {
        $nama = $params['nama'] ?? '';
        if ($nama !== '') $model = $model->where('nama', 'like', '%' . $nama . '%');
        return $model;
    }
}
