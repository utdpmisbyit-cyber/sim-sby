<?php

namespace App\Http\Controllers\Aftap;

use App\Http\Controllers\IoResourceController;
use Illuminate\Http\Request;
use App\Services\PermintaanKantongService;

class PermintaanKantongController extends IoResourceController
{
    protected $service;

    public function __construct()
    {
        $this->service = new PermintaanKantongService();
    }

    public function index()
    {
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $this->service->list()
            ]);
        }

        return view('app.aftap.permintaan_kantong.index');
    }

    public function nextNo()
    {
        $no = $this->service->generateNo();

        return response()->json([
            'no' => $no ?? ''
        ]);
    }

    public function store(Request $request)
    {
        $this->service->store($request->all());

        return response()->json(['success'=>true]);
    }

    public function edit($id)
    {
        $data = $this->service->find($id);

        if (!$data) {
            return response()->json(['success'=>false],404);
        }

        return response()->json([
            'success'=>true,
            'data'=>$data
        ]);
    }

    public function update(Request $request, $id)
    {
        $ok = $this->service->update($id, $request->all());

        if (!$ok) {
            return response()->json([
                'success'=>false,
                'message'=>'Data tidak ditemukan'
            ],404);
        }

        return response()->json(['success'=>true]);
    }
    public function show($id)
    {
        return response()->json(
            app(PermintaanKantongService::class)->find($id)
        );
    }

    public function destroy($id)
    {
        $this->service->delete($id);

        return response()->json(['success'=>true]);
    }
}