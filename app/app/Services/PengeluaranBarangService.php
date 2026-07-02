<?php

namespace App\Services;

use App\Models\PengeluaranBarang;

class PengeluaranBarangService extends IoService
{
    public function __construct()
    {
        $this->model = new PengeluaranBarang();
        $this->sort_by = ['tgl_keluar' => 'desc'];
        $this->filters = ['pengajuan_barang_id', 'barang_id', 'status', 'user_input', 'user_proses'];
    }

    public function dynamic_search($model, $params = [])
    {
        $no_trans_keluar = $params['no_trans_keluar'] ?? '';
        if ($no_trans_keluar !== '') $model = $model->where('no_trans_keluar', 'like', '%' . $no_trans_keluar . '%');

        $no_lot = $params['no_lot'] ?? '';
        if ($no_lot !== '') $model = $model->where('no_lot', 'like', '%' . $no_lot . '%');

        return $model;
    }
}
