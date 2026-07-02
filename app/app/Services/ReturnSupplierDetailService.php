<?php

namespace App\Services;

use App\Models\ReturnSupplierDetail;

class ReturnSupplierDetailService extends IoService
{
    public function __construct()
    {
        $this->model = new ReturnSupplierDetail();
        $this->sort_by = ['id' => 'asc'];
        $this->filters = ['return_supplier_id', 'barang_id'];
    }
}
