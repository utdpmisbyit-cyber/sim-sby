<?php

namespace App\Services;

use App\Models\PermintaanBarangLogistik;

class PermintaanBarangLogistikService extends IoService
{
    public array $status_options = PermintaanBarangLogistik::STATUS;

    public function __construct()
    {
        $this->model = new PermintaanBarangLogistik();
        $this->sort_by = ['tgl_terima' => 'desc'];
        $this->filters = ['kode', 'status', 'petugas_gudang_id', 'pengajuan_barang_id'];
        $this->with = ['pengajuanBarang', 'petugasGudang'];
    }
}