<?php

namespace App\Services;

use App\Models\PemeriksaanDokter;

class PemeriksaanDokterService extends IoService
{
    public array $status = PemeriksaanDokter::STATUS;
    public array $list_alasan = PemeriksaanDokter::LIST_ALASAN;
    public array $list_jenis_kantong = PemeriksaanDokter::LIST_JENIS_KANTONG;
    public array $list_tipe_jenis_kantong = PemeriksaanDokter::LIST_TIPE_JENIS_KANTONG;
    public array $question_kuisioner = PemeriksaanDokter::LIST_KUISIONER;

    public function __construct()
    {
        $this->model = new PemeriksaanDokter();
        $this->sort_by = ['created_at' => 'desc'];
        $this->filters = ['kode', 'log_donor_id', 'dokter_id', 'donor_id', 'status'];
    }

    public function dynamic_search($model, $params = [])
    {
        $step = $params['step'] ?? '';
        if ($step) $model = $model->where('step', $step);

        $date = $params['date'] ?? '';
        if ($date) $model = $model->whereDate('created_at', $date);

        return $model;
    }
}
