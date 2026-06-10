<?php

namespace App\Services;

use App\Models\DokumentasiBarang;

class DokumentasiBarangService extends IoService
{
    public function __construct()
    {
        $this->model = new DokumentasiBarang();
        $this->sort_by = ['id' => 'asc'];
        $this->filters = ['kode', 'pengajuan_barang_id'];
    }
}
