<?php

namespace App\Services;

use App\Models\PemakaianBarang;

class PemakaianBarangService extends IoService
{
    public function __construct()
    {
        $this->model = new PemakaianBarang();
        $this->sort_by = ['tgl_pemakaian' => 'desc'];
        $this->filters = ['kode', 'pengajuan_barang_id', 'barang_id', 'user_input'];
    }
}
