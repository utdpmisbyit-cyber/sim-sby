<?php

namespace App\Services;

use App\Models\PurchaseOrderDetail;

class PurchaseOrderDetailService extends IoService
{
    public function __construct()
    {
        $this->model = new PurchaseOrderDetail();
        $this->sort_by = ['id' => 'asc'];
        $this->filters = ['purchase_order_id', 'barang_id'];
    }
}
