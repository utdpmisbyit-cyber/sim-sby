<?php

namespace App\Services;

use App\Models\PurchaseOrder;

class PurchaseOrderService extends IoService
{
    public function __construct()
    {
        $this->model = new PurchaseOrder();
        $this->sort_by = ['tgl_po' => 'desc'];
        $this->filters = ['status_po', 'supplier_id', 'barang_id', 'anggaran_id', 'user_input'];
    }

    public function dynamic_search($model, $params = [])
    {
        $no_po = $params['no_po'] ?? '';
        if ($no_po !== '') $model = $model->where('no_po', 'like', '%' . $no_po . '%');
        return $model;
    }

     public function createPO(array $data): PurchaseOrder
    {
        return PurchaseOrder::create($data);
    }
 
   
    public function findPO(string $id): PurchaseOrder
    {
        return PurchaseOrder::findOrFail($id);
    }
    public function get($params = [])
{
    $query = PurchaseOrder::query();

    // pakai dynamic search jika ada parameter
    $query = $this->dynamic_search($query, $params);

    // filter bawaan dari IoService (kalau ada)
    if (!empty($params)) {
        foreach ($this->filters as $filter) {
            if (isset($params[$filter])) {
                $query->where($filter, $params[$filter]);
            }
        }
    }

    // sorting default
    foreach ($this->sort_by as $field => $direction) {
        $query->orderBy($field, $direction);
    }

    return $query->get();
}

}
