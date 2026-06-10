<?php

namespace App\Services;

use App\Models\GeneralLeadge;

class GeneralLeadgeService extends IoService
{
    public function __construct()
    {
        $this->model = new GeneralLeadge();
        $this->sort_by = ['tgl' => 'desc'];
        $this->filters = ['kode', 'coa_id', 'program_kerja_id', 'penyesuaian_id', 'trial_balance_id'];
    }

    public function dynamic_search($model, $params = [])
    {
        $no_dokumen = $params['no_dokumen'] ?? '';
        if ($no_dokumen !== '') $model = $model->where('no_dokumen', 'like', '%' . $no_dokumen . '%');

        $keterangan = $params['keterangan'] ?? '';
        if ($keterangan !== '') $model = $model->where('keterangan', 'like', '%' . $keterangan . '%');

        return $model;
    }
}
