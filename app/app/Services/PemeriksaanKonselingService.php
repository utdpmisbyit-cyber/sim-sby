<?php

namespace App\Services;

use App\Models\PemeriksaanKonseling;

class PemeriksaanKonselingService extends IoService
{
    public array $status = PemeriksaanKonseling::STATUS;

    public function __construct()
    {
        $this->model = new PemeriksaanKonseling();
        $this->sort_by = ['created_at' => 'desc'];
        $this->filters = ['kode', 'log_donor_id', 'donor_id', 'konselor_id', 'status', 'jenis_periksa'];
    }
}
