<?php

namespace App\Services;

use App\Models\PermintaanCetakUlang;

class PermintaanCetakUlangService extends IoService
{
    public array $status_options = PermintaanCetakUlang::STATUS;

    public function __construct()
    {
        $this->model = new PermintaanCetakUlang();
        $this->sort_by = ['tanggal_permohonan' => 'desc'];
        $this->filters = ['no_surat', 'nama_pemohon', 'status', 'bagian_id', 'pendataan_kantong_id'];
        $this->with = ['bagian', 'pendataanKantong'];
    }
}