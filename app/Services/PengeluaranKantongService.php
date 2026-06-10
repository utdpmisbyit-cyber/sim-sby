<?php

namespace App\Services;

use App\Models\PengeluaranKantong;

class PengeluaranKantongService extends IoService
{
    public function __construct()
    {
        $this->model = new PengeluaranKantong();
        $this->sort_by = ['no_kantong' => 'desc'];
        $this->filters = ['no_kantong', 'status', 'aktif'];
    }

    public function dynamic_search($model, $params = [])
    {
        $no_kantong = $params['no_kantong'] ?? '';
      
        if ($no_kantong !== '') {
            $model = $model->where('no_kantong', 'like', '%' . $no_kantong . '%');
        }
        return $model;
    }
}