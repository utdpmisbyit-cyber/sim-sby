<?php

namespace App\Services;

use App\Models\PemeriksaanHB;

class PemeriksaanHbService extends IoService
{
    public array $status = PemeriksaanHB::STATUS;
    public array $golongan_darah = PemeriksaanHB::GOLONGAN_DARAH;
    public array $rhesus = PemeriksaanHB::RHESUS;
    public array $lengan = PemeriksaanHB::LENGAN;
    public array $metode = PemeriksaanHB::METODE;

    public function __construct()
    {
        $this->model = new PemeriksaanHB();
        $this->sort_by = ['created_at' => 'desc'];
        $this->filters = ['kode', 'log_donor_id', 'dokter_id', 'donor_id', 'status', 'golongan_darah', 'rhesus'];
    }

    public function dynamic_search($model, $params = [])
    {
        $status_not = $params['status_not'] ?? '';
        if ($status_not) $model = $model->where('status', '<>', $status_not);

        $date = $params['date'] ?? '';
        if ($date !== '') $model->whereDate('created_at', unformatDate($date));

        return $model;
    }
    public function generateKode(): string
    {
        $last = $this->model->orderByDesc('kode')->first();
        $next = $last ? ((int)$last->kode + 1) : 1;
        return str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
