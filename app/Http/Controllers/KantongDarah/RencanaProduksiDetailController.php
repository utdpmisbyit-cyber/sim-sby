<?php

namespace App\Http\Controllers\KantongDarah;

use App\Http\Controllers\Controller;
use App\Services\RencanaProduksiDetailService;
use App\Services\RencanaProduksiService;
use Illuminate\Http\Request;

class RencanaProduksiDetailController extends Controller
{
    protected $service;
    protected $rencanaProduksiService;

    public function __construct()
    {
        $this->service = new RencanaProduksiDetailService();
        $this->rencanaProduksiService = new RencanaProduksiService();
    }

    public function store(Request $request, $rencana_produksi_id)
    {
        $request->merge(['rencana_produksi_id' => $rencana_produksi_id]);
        $no_kantong = trim((string) $request->input('no_kantong', ''));

        if ($no_kantong === '') {
            return response()->json(['error' => 'No kantong wajib diisi'], 422);
        }

        // Verify if it exists in the selected pengiriman sample
        $rencanaProduksi = \App\Models\RencanaProduksi::findOrFail($rencana_produksi_id);
        $sampleDetail = \App\Models\PengirimanSampleDetail::where('pengiriman_sample_id', $rencanaProduksi->pengiriman_sample_id)
            ->where('no_kantong', $no_kantong)
            ->first();

        if (!$sampleDetail) {
            return response()->json(['error' => 'No kantong tidak ditemukan dalam daftar pengiriman sample ini'], 422);
        }

        // Check if already exists in this plan's details
        $exists = \App\Models\RencanaProduksiDetail::where('rencana_produksi_id', $rencana_produksi_id)
            ->where('no_kantong', $no_kantong)
            ->first();

        if ($exists) {
            return response()->json(['error' => 'No kantong sudah diinput dalam rencana produksi ini'], 422);
        }

        $satelitJenisDarah = $request->input('satelit_jenis_darah');
        if (is_array($satelitJenisDarah) && count($satelitJenisDarah) > 0) {
            $inserted = [];
            foreach ($satelitJenisDarah as $satelit => $jenisDarah) {
                $inserted[] = \App\Models\RencanaProduksiDetail::create([
                    'rencana_produksi_id' => $rencana_produksi_id,
                    'no_kantong' => $no_kantong,
                    'no_satelit' => $satelit,
                    'jenis_darah' => $jenisDarah,
                ]);
            }
            return response()->json($inserted[0] ?? ['success' => true]);
        }

        return $this->service->store($request->all());
    }

    public function aftapInfo(Request $request, $rencana_produksi_id)
    {
        $rencanaProduksi = \App\Models\RencanaProduksi::findOrFail($rencana_produksi_id);
        $no_kantong = trim((string) $request->query('no_kantong', ''));

        if ($no_kantong === '') {
            return response()->json(['error' => 'No kantong wajib diisi'], 422);
        }

        // Verify if the no_kantong is part of the pengiriman sample details
        $sampleDetail = \App\Models\PengirimanSampleDetail::where('pengiriman_sample_id', $rencanaProduksi->pengiriman_sample_id)
            ->where('no_kantong', $no_kantong)
            ->first();

        if (!$sampleDetail) {
            return response()->json(['error' => 'No kantong tidak ditemukan dalam daftar pengiriman sample ini']);
        }

        $aftap = \App\Models\Aftap::with('donor')
            ->where('no_kantong', $no_kantong)
            ->first();

        if (!$aftap) {
            return response()->json(['error' => 'No kantong tidak ditemukan pada data aftap']);
        }

        $donor = $aftap->donor;
        return response()->json([
            'aftap_id' => $aftap->id,
            'no_kantong' => $aftap->no_kantong,
            'kode_aftap' => $aftap->kode,
            'jenis_donor' => $aftap->jenis_donor,
            'status_aftap' => $aftap->status,
            'jam_mulai' => $aftap->jam_mulai,
            'jam_selesai' => $aftap->jam_selesai,
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

    public function update(Request $request, $rencana_produksi_id, $id)
    {
        return $this->service->update($request->all(), $id);
    }

    public function destroy($rencana_produksi_id, $id)
    {
        $detail = \App\Models\RencanaProduksiDetail::find($id);
        if ($detail) {
            $no_kantong = $detail->no_kantong;
            \App\Models\RencanaProduksiDetail::where('rencana_produksi_id', $rencana_produksi_id)
                ->where('no_kantong', $no_kantong)
                ->delete();
            return response()->json(['success' => true]);
        }

        \App\Models\RencanaProduksiDetail::where('rencana_produksi_id', $rencana_produksi_id)
            ->where('no_kantong', $id)
            ->delete();

        return response()->json(['success' => true]);
    }
}
