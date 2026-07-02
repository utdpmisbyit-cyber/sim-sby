<?php

namespace App\Http\Controllers\Serologi;

use App\Http\Controllers\IoResourceController;
use App\Services\BarangService;
use App\Services\ReturnSupplierService;
use App\Services\SupplierService;
use Illuminate\Http\Request;

class ReturnSupplierController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new ReturnSupplierService();
        $this->viewPrefix = 'app.serologi.return_supplier';
        $this->itemVariable = 'return_supplier';

        $supplierService = new SupplierService();
        view()->share('supplier_options', $supplierService->dropdown());
    }

    public function store(Request $request)
    {
        $request->merge(['user_input' => auth()->user()->email, 'status' => 'FINISH']);
        return parent::store($request);
    }
}
