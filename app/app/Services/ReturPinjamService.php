<?php

namespace App\Services;

use App\Models\ReturPinjam;

class ReturPinjamService extends IoService
{
    public function __construct()
    {
        $this->model = new ReturPinjam();
        $this->sort_by = ['tanggal_retur' => 'desc'];
        $this->filters = ['kode', 'pinjam_barang_id', 'petugas_id', 'barang_id', 'bagian_petugas_id', 'return_supplier_id'];
    }
}
