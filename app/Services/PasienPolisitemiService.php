<?php

namespace App\Services;

use App\Models\PasienPolisitemi;

class PasienPolisitemiService extends IoService
{
    public function __construct()
    {
        $this->model = new PasienPolisitemi();
        $this->sort_by = ['nama' => 'asc'];
        $this->filters = ['kode', 'jenis_kelamin', 'agama', 'golongan_darah', 'rhesus'];
    }

    public function dynamic_search($model, $params = [])
    {
        $nama = $params['nama'] ?? '';
        if ($nama !== '') $model = $model->where('nama', 'like', '%' . $nama . '%');

        $no_ktp = $params['no_ktp'] ?? '';
        if ($no_ktp !== '') $model = $model->where('no_ktp', 'like', '%' . $no_ktp . '%');

        return $model;
    }
}
