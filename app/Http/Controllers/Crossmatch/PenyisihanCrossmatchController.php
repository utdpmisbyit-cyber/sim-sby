<?php

namespace App\Http\Controllers\Crossmatch;

use App\Http\Controllers\Controller;
use App\Models\PenyisihanCrossmatch;
use App\Services\PenyisihanCrossmatchService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PenyisihanCrossmatchController extends Controller
{
    public function __construct(protected PenyisihanCrossmatchService $service)
    {
    }

    public function index(Request $request)
    {
        $items = $this->service->paginateList(
            $request->input('search'),
            $request->input('tanggal_dari'),
            $request->input('tanggal_sampai'),
        );

        return view('app.crossmatch.penyisihan_crossmatch.index', [
            'items'   => $items,
            'filters' => $request->only(['search', 'tanggal_dari', 'tanggal_sampai']),
        ]);
    }

    /**
     * Form Tambah. Memakai view yang sama dengan edit() — lihat form.blade.php.
     */
    public function create()
    {
        return view('app.crossmatch.penyisihan_crossmatch.form', [
            'item'          => null,
            'noPenyisihan'  => $this->service->generateNoPenyisihan(),
            'alasanOptions' => $this->service->alasanOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        $this->service->store($data);

        return redirect()
            ->route('crossmatch.penyisihan_crossmatch.index')
            ->with('success', 'Penyisihan darah rusak berhasil disimpan.');
    }

    /**
     * Form Edit. Memakai view yang sama dengan create() — lihat form.blade.php.
     */
    public function edit(PenyisihanCrossmatch $penyisihanCrossmatch)
    {
        $penyisihanCrossmatch->load('details');

        return view('app.crossmatch.penyisihan_crossmatch.form', [
            'item'          => $penyisihanCrossmatch,
            'noPenyisihan'  => $penyisihanCrossmatch->no_penyisihan,
            'alasanOptions' => $this->service->alasanOptions(),
        ]);
    }

    public function update(Request $request, PenyisihanCrossmatch $penyisihanCrossmatch)
    {
        $data = $request->validate($this->rules());

        $this->service->update($penyisihanCrossmatch, $data);

        return redirect()
            ->route('crossmatch.penyisihan_crossmatch.index')
            ->with('success', 'Penyisihan darah rusak berhasil diperbarui.');
    }

    public function destroy(PenyisihanCrossmatch $penyisihanCrossmatch)
    {
        $this->service->delete($penyisihanCrossmatch);

        return redirect()
            ->route('crossmatch.penyisihan_crossmatch.index')
            ->with('success', 'Data penyisihan berhasil dihapus.');
    }

    /**
     * AJAX: dipanggil saat user scan / mengetik No Stock pada form.
     * Data diambil dari table cross_tests.
     */
    public function scanStock(Request $request)
    {
        $request->validate(['no_stock' => 'required|string']);

        try {
            $data = $this->service->findStockForScan(
                $request->input('no_stock'),
                $request->input('exclude_detail_id'),
            );

            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
            ], 422);
        }
    }

    /**
     * AJAX: nomor penyisihan berikutnya (dipakai juga untuk refresh tanpa reload halaman).
     */
    public function nextNoPenyisihan()
    {
        return response()->json([
            'no_penyisihan' => $this->service->generateNoPenyisihan(),
        ]);
    }

    /**
     * Aturan validasi store() & update() — digabung jadi 1 di sini, tidak
     * pakai FormRequest terpisah lagi.
     */
    private function rules(): array
    {
        return [
            'tanggal_penyisihan'     => ['required', 'date'],
            'petugas'                => ['nullable', 'string', 'max:100'],
            'keterangan'             => ['nullable', 'string'],

            'items'                  => ['required', 'array', 'min:1'],
            'items.*.no_stock'       => ['required', 'string', 'max:30'],
            'items.*.alasan'         => ['required', 'string', Rule::in(PenyisihanCrossmatchService::ALASAN_OPTIONS)],
            'items.*.cross_test_id'  => ['nullable', 'integer'],
            'items.*.jns_darah'      => ['nullable', 'string'],
            'items.*.gol_rh_kantong' => ['nullable', 'string'],
            'items.*.gol'            => ['nullable', 'string'],
            'items.*.rhesus'         => ['nullable', 'string'],
            'items.*.tgl_aftap'      => ['nullable', 'date'],
            'items.*.tgl_kadaluarsa' => ['nullable', 'date'],
            'items.*.status_kantong' => ['nullable', 'string'],
        ];
    }
}