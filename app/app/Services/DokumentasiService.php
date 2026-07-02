<?php

namespace App\Services;

use App\Models\Dokumentasi;

class DokumentasiService extends IoService
{
    public function __construct()
    {
        $this->model = new Dokumentasi();
        $this->sort_by = ['id' => 'asc'];
        $this->filters = ['kode', 'pengajuan_supplier_id'];
    }
}
