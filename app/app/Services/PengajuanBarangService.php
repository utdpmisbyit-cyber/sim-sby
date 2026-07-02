<?php

namespace App\Services;

use App\Models\PengajuanBarang;

class PengajuanBarangService extends IoService
{
    public array $jenis_pengajuan = PengajuanBarang::JENIS_PENGAJUAN;

    public function __construct()
    {
        $this->model = new PengajuanBarang();
        $this->sort_by = ['tgl_pengajuan' => 'desc'];
        $this->filters = ['kode', 'jenis_pengajuan', 'status', 'cabang_id', 'petugas_id', 'barang_id','bagian_id'];
        $this->with = ['barang', 'cabang', 'permintaanLogistik'];
    }
}
