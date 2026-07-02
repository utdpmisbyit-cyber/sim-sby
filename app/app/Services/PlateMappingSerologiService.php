<?php

namespace App\Services;

use App\Models\PlateMappingSerologi;

class PlateMappingSerologiService extends IoService
{
    public function __construct()
    {
        $this->model = new PlateMappingSerologi();
        $this->sort_by = ['id' => 'asc'];
        $this->filters = ['plate_serologi_id'];
    }

    public function dynamic_search($model, $params = [])
    {
        $barcode = $params['barcode'] ?? '';
        if ($barcode !== '') $model = $model->where('barcode', 'like', '%' . $barcode . '%');

        $address = $params['address'] ?? '';
        if ($address !== '') $model = $model->where('address', 'like', '%' . $address . '%');

        return $model;
    }
}
