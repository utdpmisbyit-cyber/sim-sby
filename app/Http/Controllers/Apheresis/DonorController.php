<?php

namespace App\Http\Controllers\Apheresis;

use App\Http\Controllers\IoResourceController;
use App\Http\Requests\DonorSaveRequest;
use App\Services\DonorService;
use App\Services\KecamatanService;
use App\Services\KewarganegaraanService;
use App\Services\PekerjaanService;
use App\Services\WilayahService;
use App\Services\AsalDarahService;
use App\Models\PermintaanFpup;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DonorController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new DonorService();
        $this->asalDarahService = new AsalDarahService();
        $this->viewPrefix = 'app.apheresis.donor';
        $this->itemVariable = 'donor';


        view()->share('asal_darah_options', $this->asalDarahService->dropdown());
        view()->share('jenis_kelamin_options', $this->service->jenis_kelamin);
        view()->share('agama_options', $this->service->agama);
        view()->share('golongan_darah_options', $this->service->golongan_darah);
        view()->share('rhesus_options', $this->service->rhesus);
        view()->share('golongan_darah_lain_options', $this->service->golongan_darah_lain);

        $kewarganegaraanService = new KewarganegaraanService();
        view()->share('kewarganegaraan_options', $kewarganegaraanService->dropdown());
        $wilayahService = new WilayahService();
        view()->share('wilayah_options', $wilayahService->dropdown());
        $kecamatanService = new KecamatanService();
        view()->share('kecamatan_options', $kecamatanService->dropdown());
        $pekerjaanService = new PekerjaanService();
        view()->share('pekerjaan_options', $pekerjaanService->dropdown());
    }

    public function store(Request $request)
    {
        $dataRequest = new DonorSaveRequest();
        $request->validate($dataRequest->rules(), $dataRequest->messages());
        return parent::store($request);
    }

    public function update(Request $request, $id)
    {
        $dataRequest = new DonorSaveRequest();
        $request->validate($dataRequest->rules(), $dataRequest->messages());
        return parent::update($request, $id);
    }
    public function checkFpup(Request $request)
    {
        $request->validate([
            'no_fpup' => 'required'
        ]);

        $fpup = PermintaanFpup::with('details')
            ->where('no_fpup', $request->no_fpup)
            ->first();

        if (!$fpup) {

            return response()->json([
                'success' => false,
                'message' => 'Data FPUP tidak ditemukan'
            ]);
        }

        $umur = null;

        if ($fpup->tgl_lahir) {
            $umur = Carbon::parse($fpup->tgl_lahir)->age;
        }

        return response()->json(['success' => true,
                'data' => [
                'id'           => $fpup->id,
                'fpup_id'      => $fpup->id,
                'no_fpup'      => $fpup->no_fpup,

                // DATA PASIEN
                'nama_pasien'  => $fpup->nama_pasien ?? $fpup->nama_pasien,
                'tgl_lahir'    => $fpup->tgl_lahir,
                'umur'         => $umur,

                // ALAMAT
                'alamat'       => $fpup->alamat
                                    ?? $fpup->alamat_1
                                    ?? '-',

                // GOL DARAH
                'gol_darah'    => $fpup->gol_darah,
                'rhesus'       => $fpup->rhesus,

                // DATA RS
                'tgl_minta'    => $fpup->tgl_minta,
                'no_reg'       => $fpup->no_reg,
                'kode_rs'      => $fpup->kode_rs,
                'nama_rs'      => $fpup->nama_rs,
                'jenis_rs'     => $fpup->jenis_rs,
                'bagian'       => $fpup->bagian,
                'kelas_rawat'  => $fpup->kelas_rawat,
                'nama_dokter'  => $fpup->nama_dokter,

                // DETAIL
                'details'      => $fpup->details,
            ]
        ]);
    }
    public function show($id)
{
    $donor = $this->service->find($id);

    if (!$donor) {
        return response()->json(['error' => 'Donor tidak ditemukan'], 404);
    }

    $donor->umur = $donor->tanggal_lahir
        ? \Carbon\Carbon::parse($donor->tanggal_lahir)->age
        : null;

    $donor->sudah_daftar_hari_ini = \App\Models\LogDonor::where('donor_id', $donor->id)
        ->whereDate('created_at', now()->toDateString())
        ->exists();

    return response()->json($donor);
}
public function select2Wilayah(Request $request)
{
    $q = $request->get('q', '');
    $items = \App\Models\Wilayah::query()
        ->when($q, fn($m) => $m->where('nama', 'like', "%$q%"))
        ->orderBy('nama')->limit(20)->get(['id', 'kode', 'nama']); // sesuaikan nama kolom kode

    return response()->json([
        'results' => $items->map(fn($i) => [
            'id'   => $i->id,
            'text' => $i->nama,
            'code' => str_pad($i->id, 4, '0', STR_PAD_LEFT) // atau $i->kode jika ada kolom kode
        ])
    ]);
}

public function select2Kecamatan(Request $request)
{
    $q          = $request->get('q', '');
    $wilayah_id = $request->get('wilayah_id', '');

    $items = \App\Models\Kecamatan::query()
        ->when($q,          fn($m) => $m->where('nama', 'like', "%$q%"))
        ->when($wilayah_id, fn($m) => $m->where('wilayah_id', $wilayah_id))
        ->orderBy('nama')->limit(20)->get(['id', 'nama']);

    return response()->json([
        'results' => $items->map(fn($i) => [
            'id'   => $i->id,
            'text' => $i->nama,
            'code' => str_pad($i->id, 4, '0', STR_PAD_LEFT)
        ])
    ]);
}

public function select2Pekerjaan(Request $request)
{
    $q = $request->get('q', '');
    $items = \App\Models\Pekerjaan::query()
        ->when($q, fn($m) => $m->where('nama', 'like', "%$q%"))
        ->orderBy('nama')->limit(20)->get(['id', 'nama']);

    return response()->json([
        'results' => $items->map(fn($i) => [
            'id'   => $i->id,
            'text' => $i->nama,
            'code' => str_pad($i->id, 4, '0', STR_PAD_LEFT)
        ])
    ]);
}

public function select2Kewarganegaraan(Request $request)
{
    $q = $request->get('q', '');
    $items = \App\Models\Kewarganegaraan::query()
        ->when($q, fn($m) => $m->where('nama', 'like', "%$q%"))
        ->orderBy('nama')->limit(20)->get(['id', 'nama']);

    return response()->json([
        'results' => $items->map(fn($i) => [
            'id'   => $i->id,
            'text' => $i->nama,
            'code' => str_pad($i->id, 4, '0', STR_PAD_LEFT)
        ])
    ]);
}

public function select2AsalDarah(Request $request)
{
    $q = $request->get('q', '');
    $items = \App\Models\AsalDarah::query()
        ->when($q, fn($m) => $m->where('nama', 'like', "%$q%"))
        ->orderBy('nama')->limit(20)->get(['id', 'kode', 'nama']); // sesuaikan

    return response()->json([
        'results' => $items->map(fn($i) => [
            'id'   => $i->id,
            'text' => $i->nama,
            'code' => $i->kode ?? str_pad($i->id, 4, '0', STR_PAD_LEFT)
        ])
    ]);
}


}
