<?php

namespace App\Http\Controllers\MobilUnit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PermintaanMobileUnitService;

class PermintaanMobileUnitController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new PermintaanMobileUnitService();
    }

    // INDEX — kalau AJAX kembalikan JSON, kalau bukan kembalikan view
    public function index()
    {
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data'    => $this->service->list()
            ]);
        }

        return view('app.mobil_unit.permintaan_mobil_unit.index');
    }

    // GENERATE NOMOR OTOMATIS
    public function generateNomor()
    {
        $no = $this->service->generateNomor();

        return response()->json([
            'no' => $no ?? ''
        ]);
    }

    // STORE
    public function store(Request $request)
    {
        $this->service->store($request->all());

        return response()->json(['success' => true]);
    }

    // EDIT — kembalikan data untuk diisi ke form
    public function edit($id)
    {
        $data = $this->service->find($id);

        if (!$data) {
            return response()->json(['success' => false], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $data
        ]);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $ok = $this->service->update($id, $request->all());

        if (!$ok) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json(['success' => true]);
    }

    // SHOW
    public function show($id)
    {
        $data = $this->service->find($id);

        if (!$data) {
            return response()->json(['success' => false], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $data
        ]);
    }

    // DESTROY
    public function destroy($id)
    {
        $this->service->delete($id);

        return response()->json(['success' => true]);
    }
}