<?php

namespace App\Http\Controllers\KantongDarah;

use App\Http\Controllers\Controller;
use App\Services\PenyimpananKantongService;
use App\Services\PermintaanKantongDetailService;
use Illuminate\Http\Request;

class PermintaanKantongDetailController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new PermintaanKantongDetailService();
    }

    public function store(Request $request, $permintaan_kantong_id)
    {
        $active_cabang = session('active_cabang');
        $request->merge(['permintaan_kantong_id' => $permintaan_kantong_id, 'cabang_id' => $active_cabang['id']]);
        return $this->service->store($request->all());
    }

    public function update(Request $request, $permintaan_kantong_id, $id)
    {
        return $this->service->update($request->all(), $id);
    }

    public function destroy($permintaan_kantong_id, $id)
    {
        return $this->service->delete($id);
    }
}
