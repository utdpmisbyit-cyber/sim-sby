<?php

namespace App\Services;

use App\Models\RencanaProduksiDetail;

class RencanaProduksiDetailService extends IoService
{
    public function __construct()
    {
        $this->model = new RencanaProduksiDetail();
        $this->with = ['rencanaProduksi'];
        $this->filters = ['rencana_produksi_id', 'no_kantong', 'no_satelit'];
        $this->sort_by = ['id' => 'asc'];
    }
}
