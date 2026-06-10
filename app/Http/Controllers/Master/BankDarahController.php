<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\BankDarahService;

class BankDarahController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new BankDarahService();
        $this->viewPrefix = 'app.master.bank_darah';
        $this->itemVariable = 'bank_darah';

        view()->share('list_jenis', $this->service->jenis);
    }
}
