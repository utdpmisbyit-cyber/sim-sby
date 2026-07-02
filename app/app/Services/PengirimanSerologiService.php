<?php

namespace App\Services;

use App\Models\PengirimanSerologi;

class PengirimanSerologiService extends IoService
{
    public function __construct()
    {
        $this->model = new PengirimanSerologi();
        $this->sort_by = ['created_at' => 'desc'];
        $this->filters = ['kode', 'pengirim_id', 'penerima_id'];
    }
}
