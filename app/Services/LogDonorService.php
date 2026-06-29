<?php

namespace App\Services;

use App\Models\LogDonor;

class LogDonorService extends IoService
{
    public array $next_step = LogDonor::NEXT_STEP;

    public function __construct()
    {
        $this->model = new LogDonor();
        $this->sort_by = ['created_at' => 'desc'];
        $this->filters = ['kode', 'cabang_id', 'donor_id','jenis_donor', 'petugas_registrasi_id', 'step'];
    }

    public function dynamic_search($model, $params = [])
    {
        $date = $params['date'] ?? '';
        if ($date !== '') $model->whereDate('created_at', unformatDate($date));

        if (!empty($params['step_in'])) {
            $model->whereIn('step', $params['step_in']);
        }
        $search = $params['search'] ?? '';
        if ($search !== '') {
            $model = $model->where(function ($query) use ($search) {
                $query->where('kode', 'like', '%' . $search . '%')->orWhereHas('donor', fn($donor) => $donor->where('nama', 'like', '%' . $search . '%'));
            });
        }

        return $model;
    }

    public function autoKode()
    {
        $today_count = LogDonor::whereDate('created_at', date('Y-m-d'))->withTrashed()->count() + 1;
        $today_count = strval($today_count);
        for ($i = strlen($today_count); $i <= 4; $i++) $today_count = '0' . $today_count;
        return 'A' . date('ymd') . $today_count;
    }
    public function countByDonor($donor_id)
    {
        return \App\Models\LogDonor::where('donor_id', $donor_id)->count();
    }
}
