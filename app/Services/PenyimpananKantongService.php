<?php

namespace App\Services;

use App\Models\PenyimpananKantong;

class PenyimpananKantongService extends IoService
{
    public function __construct()
    {
        $this->model = new PenyimpananKantong();
        $this->sort_by = ['id' => 'asc'];
        $this->filters = ['bagian_petugas_id', 'tipe_kantong_id'];
        $this->with = ['cabang', 'tipeKantong'];
    }

    public function filter_params($params, $id = '')
    {
        return $this->cleanNumber($params, ['jumlah']);
    }
}
