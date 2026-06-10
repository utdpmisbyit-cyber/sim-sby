<?php

namespace App\Http\Controllers\Serologi;

use App\Http\Controllers\Controller;
use App\Services\SerologiDetailService;
use App\Services\SerologiService;
use Illuminate\Http\Request;

class SerologiDetailController extends Controller
{
    protected $service;
    protected $serologiService;

    public function __construct()
    {
        $this->service = new SerologiDetailService();
        $this->serologiService = new SerologiService();
    }

    public function store(Request $request, $serologi_id)
    {
        $request->merge(['serologi_id' => $serologi_id]);

        $bulk = trim((string) $request->input('bulk_no_kantong', ''));
        if ($bulk !== '') {
            $codes = $this->service->splitBulkNoKantong($bulk);
            $created = 0;

            foreach ($codes as $code) {
                $exists = $this->service->search([
                    'serologi_id' => $serologi_id,
                    'no_kantong' => $code,
                    'ajax' => 1,
                ])->first();

                if (!$exists) {
                    $aftap = $this->service->getAftapByNoKantong($code);
                    if (!$aftap) {
                        continue;
                    }
                    $this->service->store([
                        'serologi_id' => $serologi_id,
                        'aftap_id' => $aftap->id,
                        'no_kantong' => $code,
                        'status' => 'pending',
                    ]);
                    $created++;
                }
            }

            $this->serologiService->syncStatus((int) $serologi_id);

            return response()->json(['created' => $created, 'total_input' => count($codes)]);
        }

        $aftap = $this->service->getAftapByNoKantong((string) $request->input('no_kantong', ''));
        if (!$aftap) {
            return response()->json(['error' => 'No kantong tidak ditemukan pada data aftap'], 422);
        }
        $request->merge(['aftap_id' => $aftap->id]);

        $result = $this->service->store($request->all());
        $this->serologiService->syncStatus((int) $serologi_id);
        return $result;
    }

    public function aftapInfo(Request $request, $serologi_id)
    {
        $aftap = $this->service->getAftapByNoKantong((string) $request->query('no_kantong', ''));
        if (!$aftap) {
            return response()->json(['error' => 'No kantong tidak ditemukan pada data aftap'], 404);
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

    public function update(Request $request, $serologi_id, $id)
    {
        $result = $this->service->update($request->all(), $id);
        $this->serologiService->syncStatus((int) $serologi_id);
        return $result;
    }

    public function destroy($serologi_id, $id)
    {
        $result = $this->service->delete($id);
        $this->serologiService->syncStatus((int) $serologi_id);
        return $result;
    }
}
