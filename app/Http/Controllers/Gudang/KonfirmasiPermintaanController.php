<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\IoResourceController;
use App\Services\JenisKantongService;
use App\Services\PendataanKantongService;
use App\Services\PenyimpananKantongService;
use App\Services\PermintaanKantongService;
use App\Services\PetugasService;
use App\Services\TipeKantongService;
use Illuminate\Http\Request;

class KonfirmasiPermintaanController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new PermintaanKantongService();
        $this->viewPrefix = 'app.gudang.konfirmasi_permintaan';
        $this->itemVariable = 'permintaan_kantong';
    }

    public function update(Request $request, $id)
    {
        $permintaan = $this->service->find($id);
        $pendataanKantongService = new PendataanKantongService();
        $penyimpananKantongService = new PenyimpananKantongService();
        $permintaan_kantong = $this->service->find($id);
        foreach ($permintaan_kantong->details as $detail) {
            for ($i = 1; $i <= $detail->jumlah; $i++) {
                $no_kantong = $request->input('barcode_' . $detail->id . '_' . $i);
                $pendataan = $pendataanKantongService->search(['barcode' => $no_kantong, 'first' => 1]);
                if (!empty($pendataan)) {
                    $pendataanKantongService->update(['status' => $permintaan->bagianPetugas->nama], $pendataan->id);
                }

                $penyimpananKantongService->store([
                    'bagian_petugas_id' => $permintaan->bagian_petugas_id,
                    'tipe_kantong_id' => $detail->tipe_kantong_id,
                    'no_kantong' => $no_kantong,
                    'ukuran' => $pendataan->ukuran,
                    'no_lot' => $pendataan->no_lot,
                    'flag' => 1,
                    'jumlah' => 1,
                ]);
            }
        }

        $this->service->update(['flag' => 1], $id);
        return $permintaan;
    }
}
