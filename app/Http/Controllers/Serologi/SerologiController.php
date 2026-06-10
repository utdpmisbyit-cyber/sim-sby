<?php

namespace App\Http\Controllers\Serologi;

use App\Http\Controllers\IoResourceController;
use App\Models\Petugas;
use App\Models\Serologi;
use App\Services\JenisPeriksaSerologiService;
use App\Services\MetodeSerologiService;
use App\Services\ReagenSerologiService;
use App\Services\SerologiService;
use Illuminate\Http\Request;

class SerologiController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new SerologiService();
        $this->viewPrefix = 'app.serologi.transaksi_serologi';
        $this->itemVariable = 'serologi';

        $jenisPeriksaService = new JenisPeriksaSerologiService();
        $metodeSerologiService = new MetodeSerologiService();
        $reagenSerologiService = new ReagenSerologiService();
        view()->share('jenis_periksa_serologi_options', $jenisPeriksaService->dropdown());
        view()->share('metode_serologi_options', $metodeSerologiService->dropdown());
        view()->share('reagen_serologi_options', $reagenSerologiService->dropdown());
    }

    public function create()
    {
        view()->share('generated_nomor', $this->service->generateNomor());
        view()->share('generated_group', $this->service->generateGroupCode());
        return parent::create();
    }

    public function store(Request $request)
    {
        $nomorList = $request->input('nomor_list', []);
        if (is_array($nomorList) && count($nomorList) > 0) {
            $result = $this->service->storeGrouped($request->all());
            if (($result['count'] ?? 0) === 0) {
                return response()->json(['error' => 'Minimal 1 baris nomor, jenis periksa, metode, dan reagen harus terisi'], 422);
            }
            return $result;
        }

        $request->merge([
            'status' => 'pending',
            'nomor' => $request->input('nomor') ?: $this->service->generateNomor(),
            'group' => $request->input('group') ?: $this->service->generateGroupCode(),
        ]);

        return parent::store($request);
    }

    public function edit($id)
    {
        $serologi = $this->service->find($id);
        $group_serologis = collect();

        if (!empty($serologi?->group)) {
            $group_serologis = Serologi::with(['details', 'jenisPeriksaSerologi', 'metodeSerologi', 'reagenSerologi', 'petugas', 'pemeriksaSerologi', 'diputarOleh', 'diperiksaOleh', 'disahkanOleh'])
                ->where('group', $serologi->group)
                ->orderBy('created_at')
                ->get();
        }

        return view("{$this->viewPrefix}._form", compact('serologi', 'group_serologis'));
    }

    public function duplicate(Request $request, $id)
    {
        $new = $this->service->duplicateTransaction((int) $id, $request->all());
        return response()->json($new);
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
