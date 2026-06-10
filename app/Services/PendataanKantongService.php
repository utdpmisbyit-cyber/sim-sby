<?php

namespace App\Services;

use App\Models\PendataanKantong;

class PendataanKantongService extends IoService
{
    public array $merk_kantong   = PendataanKantong::MERK_KANTONG;
    public array $jenis_kantong  = PendataanKantong::JENIS_KANTONG;
    public array $type_kantong   = PendataanKantong::TYPE_KANTONG;
    public array $ukuran_kantong = PendataanKantong::UKURAN;

    public function __construct()
    {
        $this->model = new PendataanKantong();
        $this->sort_by = ['barcode' => 'desc'];
        $this->filters = ['no_lot', 'barcode'];
    }
    
    public function dynamic_search($model, $params = [])
    {
        $barcode_like = $params['barcode_like'] ?? '';
        if ($barcode_like !== '') $model = $model->where('barcode', 'like', $barcode_like . '%');
        return $model;
    }


}