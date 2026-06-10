<?php

namespace App\Services;

use App\Models\PengirimanProduksi;

class PengirimanProduksiService extends IoService
{
    public function __construct()
    {
        $this->model = new PengirimanProduksi();
        $this->sort_by = ['created_at' => 'desc'];
        $this->filters = ['kode', 'pengirim_id', 'penerima_id'];
    }
}
