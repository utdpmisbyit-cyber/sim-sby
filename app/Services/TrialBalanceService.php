<?php

namespace App\Services;

use App\Models\TrialBalance;

class TrialBalanceService extends IoService
{
    public function __construct()
    {
        $this->model = new TrialBalance();
        $this->sort_by = ['id' => 'asc'];
        $this->filters = ['kode', 'coa_id', 'kategori1', 'kategori2', 'pos_saldo', 'pos_laporan'];
    }
}
