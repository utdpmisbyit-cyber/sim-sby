<?php

namespace App\Services;

use App\Models\BiayaCrossTest;

class BiayaCrossTestService extends IoService
{
    public function __construct()
    {
        $this->model = new BiayaCrossTest();
        $this->sort_by = ['id' => 'asc'];
        $this->filters = ['kode'];
    }

    public function filter_params($params, $id = '')
    {
        return $this->cleanNumber($params, ['harga']);
    }
}
