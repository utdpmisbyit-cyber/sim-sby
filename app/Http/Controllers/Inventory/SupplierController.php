<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\IoResourceController;
use App\Services\SupplierService;
use App\Models\Cabang;
use Illuminate\Http\Request;

class SupplierController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new SupplierService();
        $this->viewPrefix = 'app.inventory.supplier';
        $this->itemVariable = 'supplier';

        view()->share('cabang_options', Cabang::pluck('nama','id'));

    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required',
            'nama' => 'required',
            'alamat' => 'nullable',
            'no_telp' => 'nullable',
            'status' => 'required'
        ]);
        return parent::store($request);
    }

    public function update(Request $request, $id)
    {
        return parent::update($request,$id);
    }
}