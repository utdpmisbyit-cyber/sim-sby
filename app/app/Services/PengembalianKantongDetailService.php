<?php

namespace App\Services;

use App\Models\PengembalianKantongDetail;

class PengembalianKantongDetailService extends IoService
{
    public function __construct()
    {
        $this->model = new PengembalianKantongDetail();
        $this->filters = ['pengembalian_kantong_id', 'tipe_kantong_id', 'flag'];
        $this->with = ['tipe_kantong'];
    }
}
