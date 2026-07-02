<?php

namespace App\Services;

use App\Models\PinjamBarang;

class PinjamBarangService extends IoService
{
    public function __construct()
    {
        $this->model = new PinjamBarang();
        $this->sort_by = ['tanggal_pinjam' => 'desc'];
        $this->filters = ['kode', 'barang_id', 'petugas_id', 'bagian_id', 'status'];
    }
}
