<?php

namespace App\Services;

use App\Models\PengajuanSupplier;

class PengajuanSupplierService extends IoService
{
    public function __construct()
    {
        $this->model = new PengajuanSupplier();
        $this->sort_by = ['tgl_pengajuan' => 'desc'];
        $this->filters = ['kode', 'jenis_pengajuan', 'status', 'supplier_id', 'user_input', 'user_proses'];
    }
}
