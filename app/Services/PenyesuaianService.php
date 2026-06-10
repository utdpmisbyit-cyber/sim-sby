<?php

namespace App\Services;

use App\Models\Penyesuaian;

class PenyesuaianService extends IoService
{
    public array $jenis_saldo = Penyesuaian::JENIS_SALDO;

    public function __construct()
    {
        $this->model = new Penyesuaian();
        $this->sort_by = ['tgl' => 'desc'];
        $this->filters = ['kode', 'jenis_saldo', 'program_kerja_id'];
    }

    public function dynamic_search($model, $params = [])
    {
        $keterangan = $params['keterangan'] ?? '';
        if ($keterangan !== '') $model = $model->where('keterangan', 'like', '%' . $keterangan . '%');
        return $model;
    }
}
