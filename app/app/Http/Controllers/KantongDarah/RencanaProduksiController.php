<?php

namespace App\Http\Controllers\KantongDarah;

use App\Http\Controllers\IoResourceController;
use App\Models\Petugas;
use App\Models\RencanaProduksi;
use App\Services\PenyimpananKantongService;
use App\Services\RencanaProduksiService;
use App\Services\TipeKantongService;
use Illuminate\Http\Request;

class RencanaProduksiController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new RencanaProduksiService();
        $this->viewPrefix = 'app.kantong_darah.rencana_produksi';
        $this->itemVariable = 'rencana_produksi';

        $tipeKantongService = new TipeKantongService();
        view()->share('tipe_kantong_options', $tipeKantongService->dropdown());
        view()->share('pengiriman_sample_options', \App\Models\PengirimanSample::pluck('no_fpd', 'id')->toArray());
    }

    public function create()
    {
        $aturan_satelits = collect();
        $satelit_options = collect();
        return view("{$this->viewPrefix}._form", compact('aturan_satelits', 'satelit_options'));
    }

    public function store(Request $request)
    {
        $pengiriman_sample_id = $request->input('pengiriman_sample_id');
        $sample = \App\Models\PengirimanSample::find($pengiriman_sample_id);
        $no_kantong = $sample->detail[0]->no_kantong;
        $penyimpananKantongService = new PenyimpananKantongService();
        $penyimpanan_kantong = $penyimpananKantongService->find($no_kantong, 'no_kantong');
        $tipeKantong = $penyimpanan_kantong->tipeKantong;
        $tipe_kantong_id = $penyimpanan_kantong->tipe_kantong_id;

        if (empty($tipeKantong)) {
            return response()->json("Tipe Kantong Kosong " . json_encode($penyimpanan_kantong), 401);
        }

        $request->merge(['tipe_kantong_id' => $tipe_kantong_id]);

        $rencanaProduksi = $this->service->store($request->all());
        if (!empty($rencanaProduksi['errors'])) {
            return response()->json($rencanaProduksi['errors'], 401);
        }

        if ($rencanaProduksi && isset($rencanaProduksi->id)) {
            // Get all satelits for this tipe_kantong
            $aturan_satelits = \Illuminate\Support\Facades\DB::table('aturan_satelit')
                ->where('typektg', $tipeKantong->nama)
                ->get();

            $satelits = $aturan_satelits->pluck('satelit')->unique();

            // Get all details from pengiriman_sample
            $sampleDetails = \App\Models\PengirimanSampleDetail::where('pengiriman_sample_id', $pengiriman_sample_id)->get();

            foreach ($sampleDetails as $sd) {
                foreach ($satelits as $sat) {
                    \App\Models\RencanaProduksiDetail::create([
                        'rencana_produksi_id' => $rencanaProduksi->id,
                        'no_kantong' => $sd->no_kantong,
                        'no_satelit' => $sat,
                        'jenis_darah' => null, // empty initially
                    ]);
                }
            }
        }

        return $rencanaProduksi;
    }

    public function update(Request $request, $id)
    {
        $rencanaProduksi = $this->service->update($request->all(), $id);
        if (!empty($rencanaProduksi['errors'])) {
            return response()->json($rencanaProduksi['errors'], 401);
        }

        // Update all jenis_darah on each satelit on each rencana produksi detail
        $satelitJenisDarah = $request->input('satelit_jenis_darah');
        if (is_array($satelitJenisDarah) && count($satelitJenisDarah) > 0) {
            foreach ($satelitJenisDarah as $satelit => $jenisDarah) {
                \App\Models\RencanaProduksiDetail::where('rencana_produksi_id', $id)
                    ->where('no_satelit', $satelit)
                    ->update(['jenis_darah' => $jenisDarah]);
            }
        }

        return $rencanaProduksi;
    }

    public function edit($id)
    {
        $rencana_produksi = $this->service->find($id);

        $aturan_satelits = collect();
        $satelit_options = collect();
        $mapped_satelit = [];
        if ($rencana_produksi && $rencana_produksi->tipeKantong) {
            $aturan_satelits = \Illuminate\Support\Facades\DB::table('aturan_satelit')
                ->where('typektg', $rencana_produksi->tipeKantong->nama)
                ->get();
            $satelit_options = $aturan_satelits->pluck('satelit')->unique()->sort();
            foreach ($aturan_satelits as $aturan_satelit) $mapped_satelit[$aturan_satelit->satelit][] = $aturan_satelit;
        }

        return view("{$this->viewPrefix}._form", compact('rencana_produksi', 'aturan_satelits', 'satelit_options', 'mapped_satelit'));
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

    public function pengirimanSampleInfo($id)
    {
        $sample = \App\Models\PengirimanSample::find($id);
        if (!$sample) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $tipeKantong = \App\Models\TipeKantong::where('nama', $sample->type_kantong)->first();

        return response()->json([
            'id' => $sample->id,
            'type_kantong' => $sample->type_kantong,
            'tipe_kantong_id' => $tipeKantong ? $tipeKantong->id : null,
            'tipe_kantong_nama' => $tipeKantong ? $tipeKantong->nama : null,
        ]);
    }
}
