<?php

namespace App\Services;

use App\Models\KantongDarah;

class KantongDarahService extends IoService
{
    public function __construct()
    {
        $this->model = new KantongDarah();
        $this->sort_by = ['nama' => 'asc'];
        $this->filters = ['kode', 'cabang_id', 'jenis_kantong', 'tipe_jenis_kantong', 'ukuran_kantong', 'merk'];
    }

    public function dynamic_search($model, $params = [])
    {
        $nama = $params['nama'] ?? '';
        if ($nama !== '') $model = $model->where('nama', 'like', '%' . $nama . '%');
        return $model;
    }
}
