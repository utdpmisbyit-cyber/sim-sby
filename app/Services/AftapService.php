<?php

namespace App\Services;

use App\Models\Aftap;

class AftapService extends IoService
{
    public array $status = Aftap::STATUS;
    public array $cara_ambil = Aftap::CARA_AMBIL;
    public array $jenis_donor = Aftap::JENIS_DONOR;
    public array $reaksi_donor = Aftap::REAKSI_DONOR;

    public function __construct()
    {
        $this->model = new Aftap();
        $this->sort_by = ['created_at' => 'desc'];
        $this->filters = ['kode', 'log_donor_id', 'dokter_id', 'donor_id', 'asal_darah_id', 'status'];
    }

    public function dynamic_search($model, $params = [])
    {
        $status_not = $params['status_not'] ?? '';
        if ($status_not) $model = $model->where('status', '<>', $status_not);

        $date = $params['date'] ?? '';
        if ($date !== '') $model->whereDate('created_at', unformatDate($date));

        return $model;
    }

    public function filter_params($params, $id = '')
    {
        if (!empty($params['jam_mulai'])) $params['jam_mulai'] = date('Y-m-d') . ' ' . $params['jam_mulai'];
        if (!empty($params['jam_selesai'])) $params['jam_selesai'] = date('Y-m-d') . ' ' . $params['jam_selesai'];

        return $params;
    }
}
