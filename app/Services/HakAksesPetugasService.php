<?php

namespace App\Services;

use App\Models\HakAksesPetugas;

class HakAksesPetugasService extends IoService
{
    public function __construct()
    {
        $this->model = new HakAksesPetugas();
        $this->sort_by = ['id' => 'asc'];
        $this->filters = ['petugas_id', 'hak_akses_id'];
    }
}
