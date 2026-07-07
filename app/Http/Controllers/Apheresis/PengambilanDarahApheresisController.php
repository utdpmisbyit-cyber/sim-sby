<?php

namespace App\Http\Controllers\Apheresis;

use App\Http\Controllers\Controller;
use App\Http\Requests\Apheresis\PengambilanDarahApheresisRequest;
use App\Models\PengambilanDarahApheresis;
use App\Services\PengambilanDarahApheresisService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PengambilanDarahApheresisController extends Controller
{
    public function __construct(protected PengambilanDarahApheresisService $service)
    {
    }

    public function index(Request $request): View
    {
        $items = $this->service->list($request->get('q'));

        return view('app.apheresis.pengambilan_darah.index', [
            'items' => $items,
            'q'     => $request->get('q'),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $items = $this->service->list($request->get('q'));

        return response()->json([
            'html' => view('app.apheresis.pengambilan_darah._table', ['items' => $items])->render(),
        ]);
    }

    public function create(): View
    {
        return view('app.apheresis.pengambilan_darah.form', [
            'lembarKerja' => new PengambilanDarahApheresis([
                'no_transaksi' => $this->service->generateKode(),
            ]),
            'isEdit' => false,
        ]);
    }

    public function store(PengambilanDarahApheresisRequest $request): RedirectResponse
    {
        $header = $this->service->create($request->validated());

        return redirect()
            ->route('apheresis.pengambilan_darah.edit', $header->id)
            ->with('success', "Lembar kerja {$header->no_transaksi} berhasil disimpan.");
    }

    public function edit(int $id): View
    {
        $header = $this->service->find($id);

        return view('app.apheresis.pengambilan_darah.form', [
            'lembarKerja' => $header,
            'isEdit'      => true,
        ]);
    }

    public function update(PengambilanDarahApheresisRequest $request, int $id): RedirectResponse
    {
        $header = $this->service->find($id);
        $this->service->update($header, $request->validated());

        return redirect()
            ->route('apheresis.pengambilan_darah.edit', $header->id)
            ->with('success', 'Lembar kerja berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $header = $this->service->find($id);
        $this->service->delete($header);

        return redirect()
            ->route('apheresis.pengambilan_darah.index')
            ->with('success', 'Lembar kerja berhasil dihapus.');
    }

    public function generateKode(): JsonResponse
    {
        return response()->json([
            'no_transaksi' => $this->service->generateKode(),
            'server_date'  => now()->format('Y-m-d H:i'),
        ]);
    }

    /** Cari referensi data donor untuk auto-fill (dipanggil saat No Donor di-scan/diisi) */
    public function searchDonor(Request $request): JsonResponse
    {
        $request->validate(['no_donor' => 'required|string']);

        $data = $this->service->findDonorReference($request->no_donor);

        if (!$data) {
            return response()->json(['found' => false], 404);
        }

        return response()->json(['found' => true, 'data' => $data]);
    }

    /** Cari data dari modul Sampling Pra Donor untuk auto-fill (dipanggil saat No Sampling diisi) */
    public function searchSampling(Request $request): JsonResponse
    {
        $request->validate(['no_sampling' => 'required|string']);

        $data = $this->service->findSamplingReference($request->no_sampling);

        if (!$data) {
            return response()->json(['found' => false], 404);
        }

        return response()->json(['found' => true, 'data' => $data]);
    }

    /**
     * Pencarian petugas untuk dropdown select2.
     * Dipakai untuk field 'Petugas' (simpan sebagai ID) maupun field 'Operator' (simpan sebagai teks),
     * bedakan lewat parameter ?as_text=1.
     */
    public function searchPetugas(Request $request): JsonResponse
    {
        $q = $request->get('q');
        $asText = $request->boolean('as_text');

        try {
            $hasKode = \Illuminate\Support\Facades\Schema::hasColumn('users', 'kode_petugas');

            $columns = array_values(array_filter(['id', 'name', $hasKode ? 'kode_petugas' : null]));

            $query = \Illuminate\Support\Facades\DB::table('users');
            if ($q) {
                $query->where(function ($qq) use ($q, $hasKode) {
                    $qq->where('name', 'like', "%{$q}%");
                    if ($hasKode) {
                        $qq->orWhere('kode_petugas', 'like', "%{$q}%");
                    }
                });
            }

            $users = $query->orderBy('name')->limit(20)->get($columns);

            $results = $users->map(function ($u) use ($hasKode, $asText) {
                $label = trim((($hasKode && !empty($u->kode_petugas)) ? $u->kode_petugas . ' - ' : '') . $u->name);
                return [
                    'id'   => $asText ? $label : $u->id,
                    'text' => $label,
                ];
            });

            return response()->json($results);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('searchPetugas gagal: ' . $e->getMessage());
            // Jangan sampai 500 ke frontend - kembalikan list kosong saja supaya select2 tetap jalan
            return response()->json([]);
        }
    }
}