<?php

namespace App\Http\Controllers\KantongDarah;

use App\Http\Controllers\IoResourceController;
use App\Services\JenisKantongService;
use App\Services\PengembalianKantongService;
use App\Services\PenyimpananKantongService;
use App\Services\PetugasService;
use App\Services\TipeKantongService;
use Illuminate\Http\Request;

class PengembalianKantongController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new PengembalianKantongService();
        $this->viewPrefix = 'app.kantong_darah.pengembalian_kantong';
        $this->itemVariable = 'pengembalian_kantong';

        $petugasService = new PetugasService();
        view()->share('petugas_options', $petugasService->dropdown(['nama_jabatan' => 'Admin']));
        $jenisKantongService = new JenisKantongService();
        view()->share('jenis_kantong_options', $jenisKantongService->dropdown());
        $tipeKantongService = new TipeKantongService();
        view()->share('tipe_kantong_options', $tipeKantongService->search());
    }

    public function store(Request $request)
    {
        $petugasService = new PetugasService();
        $petugas = $petugasService->find($request->input('petugas_id'));
        if (!empty($petugas)) $request->merge(['bagian_petugas_id' => $petugas->bagian_id]);

        return parent::store($request);
    }

    public function confirm(Request $request, $id)
    {
        $permintaan = $this->service->update($request->all(), $id);
        $penyimpananKantongService = new PenyimpananKantongService();
        foreach ($permintaan->details as $detail) {
            $penyimpananKantongService->store([
                'bagian_petugas_id' => $permintaan->bagian_petugas_id,
                'tipe_kantong_id' => $detail->tipe_kantong_id,
                'jumlah' => -1 * $detail->jumlah,
            ]);
        }
    }

}
