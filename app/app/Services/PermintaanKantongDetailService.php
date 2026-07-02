<?php

namespace App\Services;

use App\Models\PermintaanKantongDetail;

class PermintaanKantongDetailService extends IoService
{
    public function __construct()
    {
        $this->model = new PermintaanKantongDetail();
        $this->filters = ['permintaan_kantong_id', 'tipe_kantong_id', 'flag'];
        $this->with = ['tipe_kantong'];
        
    }
    protected $table = 'permintaan_kantong_detail';
    public $timestamps = false;
   
}
