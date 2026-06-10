<?php

namespace App\Http\Controllers\Serologi;

use App\Http\Controllers\IoResourceController;
use App\Services\BarangService;
use App\Services\PermintaanSupplierService;
use App\Services\SupplierService;
use Illuminate\Http\Request;

class PermintaanSupplierController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new PermintaanSupplierService();
        $this->viewPrefix = 'app.serologi.permintaan_supplier';
        $this->itemVariable = 'permintaan_supplier';

        $supplierService = new SupplierService();
        view()->share('supplier_options', $supplierService->dropdown());
        $barangService = new BarangService();
        view()->share('barang_options', $barangService->dropdown());
    }

    public function store(Request $request)
    {
        $request->merge(['user_input' => auth()->user()->email, 'status' => 'FINISH']);
        return parent::store($request);
    }
}
