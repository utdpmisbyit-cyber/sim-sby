<?php

namespace App\Services;

use App\Models\BankDarah;

class BankDarahService extends IoService
{
    public array $jenis = BankDarah::JENIS;

    public function __construct()
    {
        $this->model = new BankDarah();
        $this->sort_by = ['nama' => 'asc'];
        $this->filters = ['kode', 'jenis'];
    }

    public function dynamic_search($model, $params = [])
    {
        $nama = $params['nama'] ?? '';
        if ($nama !== '') $model = $model->where('nama', 'like', '%' . $nama . '%');
        return $model;
    }
}
