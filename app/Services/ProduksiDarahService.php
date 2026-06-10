<?php

namespace App\Services;

use App\Models\ProduksiDarah;

class ProduksiDarahService extends IoService
{
    public array $status = ProduksiDarah::STATUS;

    public function __construct()
    {
        $this->model = new ProduksiDarah();
        $this->sort_by = ['created_at' => 'desc'];
        $this->filters = ['kode', 'status', 'pengiriman_produksi_id'];
    }

    public function dynamic_search($model, $params = [])
    {
        $barcode = $params['barcode'] ?? '';
        if ($barcode !== '') $model = $model->where('barcode', 'like', '%' . $barcode . '%');
        return $model;
    }
}
