<?php

namespace App\Services;

use App\Models\WorksheetUmumSerologi;

class WorksheetUmumSerologiService extends IoService
{
    public function __construct()
    {
        $this->model = new WorksheetUmumSerologi();
        $this->sort_by = ['created_at' => 'desc'];
        $this->filters = ['kode', 'plate_serologi_id', 'metode_serologi_id', 'reagen_serologi_id', 'jenis_periksa'];
    }
}
