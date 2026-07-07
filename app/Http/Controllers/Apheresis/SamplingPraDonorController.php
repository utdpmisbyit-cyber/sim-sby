<?php

namespace App\Http\Controllers\Apheresis;

use App\Http\Controllers\Controller;
use App\Http\Requests\Apheresis\SamplingPraDonorRequest;
use App\Models\SamplingPraDonor;
use App\Services\SamplingPraDonorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SamplingPraDonorController extends Controller
{
    public function __construct(protected SamplingPraDonorService $service)
    {
    }

    /** Tampilan index / daftar sampling pra donor */
    public function index(Request $request): View
    {
        $items = $this->service->list($request->get('q'));

        return view('app.apheresis.sampling_pra_donor.index', [
            'items' => $items,
            'q'     => $request->get('q'),
        ]);
    }

    /** Pencarian ajax untuk tabel index */
    public function search(Request $request): JsonResponse
    {
        $items = $this->service->list($request->get('q'));

        return response()->json([
            'html' => view('app.apheresis.sampling_pra_donor._table', ['items' => $items])->render(),
        ]);
    }

    /** Form input baru */
    public function create(): View
    {
        return view('app.apheresis.sampling_pra_donor.form', [
            'sampling'     => new SamplingPraDonor(['no_transaksi' => $this->service->generateKode()]),
            'ranges'       => $this->service->normalRanges(),
            'alasanOptions'=> SamplingPraDonor::ALASAN_OPTIONS,
            'isEdit'       => false,
        ]);
    }

    /** Simpan data baru */
    public function store(SamplingPraDonorRequest $request): RedirectResponse
    {
        $sampling = $this->service->create($request->validated());

        return redirect()
            ->route('apheresis.sampling_pra_donor.edit', $sampling->id)
            ->with('success', "Data sampling {$sampling->no_transaksi} berhasil disimpan.");
    }

    /** Form edit data */
    public function edit(int $id): View
    {
        $sampling = $this->service->find($id);

        return view('app.apheresis.sampling_pra_donor.form', [
            'sampling'      => $sampling,
            'ranges'        => $this->service->normalRanges(),
            'alasanOptions' => SamplingPraDonor::ALASAN_OPTIONS,
            'isEdit'        => true,
        ]);
    }

    /** Update data */
    public function update(SamplingPraDonorRequest $request, int $id): RedirectResponse
    {
        $sampling = $this->service->find($id);
        $this->service->update($sampling, $request->validated());

        return redirect()
            ->route('apheresis.sampling_pra_donor.edit', $sampling->id)
            ->with('success', 'Data sampling berhasil diperbarui.');
    }

    /** Hapus data */
    public function destroy(int $id): RedirectResponse
    {
        $sampling = $this->service->find($id);
        $this->service->delete($sampling);

        return redirect()
            ->route('apheresis.sampling_pra_donor.index')
            ->with('success', 'Data sampling berhasil dihapus.');
    }

    /** Generate No Transaksi baru (dipanggil via tombol '+' di header form) */
    public function generateKode(): JsonResponse
    {
        return response()->json([
            'no_transaksi' => $this->service->generateKode(),
            'server_date'  => now()->format('Y-m-d H:i'),
        ]);
    }

    /** Cari referensi data donor untuk auto-fill (dipanggil saat No Donor diisi) */
    public function searchDonor(Request $request): JsonResponse
    {
        $request->validate(['no_donor' => 'required|string']);

        $data = $this->service->findDonorReference($request->no_donor);

        if (!$data) {
            return response()->json(['found' => false], 404);
        }

        return response()->json(['found' => true, 'data' => $data]);
    }
}