<?php

namespace App\Services;

use App\Models\ProduksiKantongDarah;

class ProduksiKantongDarahService extends IoService
{
    public function __construct()
    {
        $this->model = new ProduksiKantongDarah();
        $this->sort_by = ['created_at' => 'desc'];
        $this->filters = ['kode', 'kantong_darah_id'];
    }

    public function dynamic_search($model, $params = [])
    {
        $nomor_lot = $params['nomor_lot'] ?? '';
        if ($nomor_lot !== '') $model = $model->where('nomor_lot', 'like', '%' . $nomor_lot . '%');
        return $model;
    }
}
