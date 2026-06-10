<?php

namespace App\Services;

use App\Models\Barang;

class BarangService extends IoService
{
    public array $jenis_barang = Barang::JENIS_BARANG;
 

    public function __construct()
    {
        $this->model = new Barang();
        $this->sort_by = ['nama' => 'asc'];
        $this->filters = ['kode', 'jenis_barang', 'cabang_id'];
    }

    public function dynamic_search($model, $params = [])
    {
        $nama = $params['nama'] ?? '';
        if ($nama !== '') $model = $model->where('nama', 'like', '%' . $nama . '%');
        return $model;
    }
}
