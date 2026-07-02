<?php

namespace App\Services;

use App\Models\Coa;

class CoaService extends IoService
{
    public array $possaldo = Coa::POSSALDO;
    public array $poslaporan = Coa::POSLAPORAN;

    public function __construct()
    {
        $this->model = new Coa();
        $this->sort_by = ['kd_coa' => 'asc'];
        $this->filters = ['kd_coa', 'kategori_1', 'kategori_2', 'possaldo', 'poslaporan'];
    }

    public function dynamic_search($model, $params = [])
    {
        $nama_akun = $params['nama_akun'] ?? '';
        if ($nama_akun !== '') $model = $model->where('nama_akun', 'like', '%' . $nama_akun . '%');
        return $model;
    }
}
