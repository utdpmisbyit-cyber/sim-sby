<?php

namespace App\Services;

use App\Models\PlateSerologi;

class PlateSerologiService extends IoService
{
    public function __construct()
    {
        $this->model = new PlateSerologi();
        $this->sort_by = ['id' => 'asc'];
        $this->filters = ['kode'];
    }
}
