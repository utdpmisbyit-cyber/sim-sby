<?php

namespace App\Services;

use App\Models\KantongDarahPermintaanAftap;

class KantongDarahPermintaanAftapService extends IoService
{
    public function __construct()
    {
        $this->model = new KantongDarahPermintaanAftap();
        $this->sort_by = ['created_at' => 'desc'];
        $this->filters = ['permintaan_aftap_id', 'kantong_darah_id'];
    }
}
