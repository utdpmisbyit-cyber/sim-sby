<?php

namespace App\Http\Controllers\Serologi;

use App\Http\Controllers\IoResourceController;
use App\Models\Donor;
use App\Models\Petugas;
use App\Services\LitbangService;
use Illuminate\Http\Request;

class KonfirmasiLitbangController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new LitbangService();
        $this->viewPrefix = 'app.serologi.konfirmasi_litbang';
        $this->itemVariable = 'konfirmasi_litbang';

        view()->share('golongan_darah_options', array_combine(Donor::GOLONGAN_DARAH, Donor::GOLONGAN_DARAH));
        view()->share('rhesus_options', array_combine(Donor::RHESUS, Donor::RHESUS));
    }

    public function update(Request $request, $id)
    {
        $request->merge([
            'status' => 'selesai',
            'tanggal_konfirmasi' => $request->input('tanggal_konfirmasi') ?: date('d-m-Y'),
        ]);

        return parent::update($request, $id);
    }

    public function petugasByKode(Request $request)
    {
        $kode = trim((string) $request->query('kode', ''));
        if ($kode === '') {
            return response()->json(['error' => 'Kode petugas wajib diisi'], 422);
        }

        $petugas = Petugas::query()->where('kode', $kode)->first();
        if (!$petugas) {
            return response()->json(['error' => 'Kode petugas tidak ditemukan'], 404);
        }

        return response()->json([
            'id' => $petugas->id,
            'kode' => $petugas->kode,
            'nama' => $petugas->nama,
        ]);
    }
}
