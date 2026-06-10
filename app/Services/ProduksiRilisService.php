<?php

namespace App\Services;

use App\Models\ProduksiDarah;

class ProduksiRilisService extends IoService
{
    public function __construct()
    {
        $this->model = new ProduksiDarah();
        $this->sort_by = ['kode' => 'desc', 'created_at' => 'desc'];
        $this->filters = ['status', 'pengiriman_produksi_id'];
    }

    public function dynamic_search($model, $params = [])
    {
        $keyword = trim($params['keyword'] ?? '');
        if ($keyword !== '') {
            $model = $model->where(function ($q) use ($keyword) {
                $q->where('kode', 'like', '%' . $keyword . '%')
                    ->orWhere('barcode', 'like', '%' . $keyword . '%')
                    ->orWhere('status', 'like', '%' . $keyword . '%');
            });
        }
        return $model;
    }
}
