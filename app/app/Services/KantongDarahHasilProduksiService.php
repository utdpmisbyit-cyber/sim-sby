<?php

namespace App\Services;

use App\Models\KantongDarahHasilProduksi;

class KantongDarahHasilProduksiService extends IoService
{
    public array $pemilik = KantongDarahHasilProduksi::PEMILIK;

    public function __construct()
    {
        $this->model = new KantongDarahHasilProduksi();
        $this->sort_by = ['created_at' => 'desc'];
        $this->filters = ['kode', 'kantong_darah_id', 'log_donor_id', 'produksi_kantong_darah_id', 'pemilik'];
    }
}
