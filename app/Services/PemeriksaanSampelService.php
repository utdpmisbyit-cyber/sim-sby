<?php

namespace App\Services;

use App\Models\PemeriksaanSampel;

class PemeriksaanSampelService extends IoService
{
    public array $status = PemeriksaanSampel::STATUS;

    public function __construct()
    {
        $this->model = new PemeriksaanSampel();
        $this->sort_by = ['created_at' => 'desc'];
        $this->filters = ['kode', 'status', 'pengiriman_serologi_id'];
    }

    public function dynamic_search($model, $params = [])
    {
        $barcode = $params['barcode'] ?? '';
        if ($barcode !== '') $model = $model->where('barcode', 'like', '%' . $barcode . '%');
        return $model;
    }
}
