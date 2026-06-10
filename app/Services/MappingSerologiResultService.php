<?php

namespace App\Services;

use App\Models\MappingSerologiResult;

class MappingSerologiResultService extends IoService
{
    public function __construct()
    {
        $this->model = new MappingSerologiResult();
        $this->sort_by = ['created_at' => 'desc'];
        $this->filters = ['plate_mapping_serologi_id', 'worksheet_umum_serologi_id', 'kesimpulan'];
    }
}
