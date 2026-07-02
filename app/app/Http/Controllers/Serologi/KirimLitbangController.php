<?php

namespace App\Http\Controllers\Serologi;

use App\Http\Controllers\IoResourceController;
use App\Models\Aftap;
use App\Models\Litbang;
use App\Models\Petugas;
use App\Services\LitbangService;
use Illuminate\Http\Request;

class KirimLitbangController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new LitbangService();
        $this->viewPrefix = 'app.serologi.kirim_litbang';
        $this->itemVariable = 'kirim_litbang';
    }

    public function store(Request $request)
    {
        $noKantong = trim((string) $request->input('no_kantong', ''));
        if ($noKantong === '') {
            return response()->json(['errors' => ['no_kantong' => ['No. Kantong wajib diisi.']]], 422);
        }

        // Check unique constraint: 1 Litbang, 1 No Kantong
        $exists = Litbang::where('no_kantong', $noKantong)->exists();
        if ($exists) {
            return response()->json(['errors' => ['no_kantong' => ['No. Kantong sudah dikirim ke Litbang.']]], 422);
        }

        $aftap = Aftap::where('no_kantong', $noKantong)->first();
        if (!$aftap) {
            return response()->json(['errors' => ['no_kantong' => ['No. Kantong tidak ditemukan di data aftap.']]], 422);
        }

        $request->merge([
            'aftap_id' => $aftap->id,
            'donor_id' => $aftap->donor_id,
            'status' => 'pending',
        ]);

        return parent::store($request);
    }

    public function aftapInfo(Request $request)
    {
        $noKantong = trim((string) $request->query('no_kantong', ''));
        if ($noKantong === '') {
            return response()->json(['error' => 'No. Kantong wajib diisi'], 422);
        }

        $aftap = Aftap::query()
            ->with('donor')
            ->where('no_kantong', $noKantong)
            ->first();

        if (!$aftap) {
            return response()->json(['error' => 'No. Kantong tidak ditemukan pada data aftap'], 404);
        }

        $donor = $aftap->donor;
        return response()->json([
            'aftap_id' => $aftap->id,
            'no_kantong' => $aftap->no_kantong,
            'kode_aftap' => $aftap->kode,
            'jenis_donor' => $aftap->jenis_donor,
            'status_aftap' => $aftap->status,
            'donor' => $donor ? [
                'id' => $donor->id,
                'kode' => $donor->kode,
                'nama' => $donor->nama,
                'golongan_darah' => $donor->golongan_darah,
                'rhesus' => $donor->rhesus,
                'jenis_kelamin' => $donor->jenis_kelamin,
            ] : null,
        ]);
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
