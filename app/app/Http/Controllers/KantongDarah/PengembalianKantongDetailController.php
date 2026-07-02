<?php

namespace App\Http\Controllers\KantongDarah;

use App\Http\Controllers\Controller;
use App\Services\PengembalianKantongDetailService;
use App\Services\PenyimpananKantongService;
use Illuminate\Http\Request;

class PengembalianKantongDetailController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new PengembalianKantongDetailService();
    }

    public function store(Request $request, $pengembalian_kantong_id)
    {
        $active_cabang = session('active_cabang');
        $request->merge(['pengembalian_kantong_id' => $pengembalian_kantong_id, 'cabang_id' => $active_cabang['id']]);
        return $this->service->store($request->all());
    }

    public function update(Request $request, $pengembalian_kantong_id, $id)
    {
        return $this->service->update($request->all(), $id);
    }

    public function destroy($pengembalian_kantong_id, $id)
    {
        return $this->service->delete($id);
    }


}
