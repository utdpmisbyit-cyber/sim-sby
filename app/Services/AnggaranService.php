<?php

namespace App\Services;

use App\Models\Anggaran;

class AnggaranService extends IoService
{
    public function __construct()
    {
        $this->model = new Anggaran();
        $this->sort_by = ['tahun_anggaran' => 'desc'];
        $this->filters = ['kode', 'tahun_anggaran', 'user_input'];
    }

    public function dynamic_search($model, $params = [])
    {
        $keterangan = $params['keterangan'] ?? '';
        if ($keterangan !== '') $model = $model->where('keterangan', 'like', '%' . $keterangan . '%');
        return $model;
    }
}
