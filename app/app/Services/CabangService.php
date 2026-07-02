<?php

namespace App\Services;

use App\Models\Cabang;

class CabangService extends IoService
{
    public array $list_jenis = Cabang::LIST_JENIS;
    public function __construct()
    {
        $this->model = new Cabang();
        $this->sort_by = ['nama' => 'asc'];
        $this->filters = ['kode', 'status'];
    }

    public function dynamic_search($model, $params = [])
    {
        $nama = $params['nama'] ?? '';
        if ($nama !== '') $model = $model->where('nama', 'like', '%' . $nama . '%');
        return $model;
    }
}
