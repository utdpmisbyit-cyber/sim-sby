<?php

namespace App\Http\Controllers\Finance\Kasir;

use App\Http\Controllers\Controller;
use App\Http\Requests\PembayaranDroppingExternalRequest;
use App\Models\PembayaranDroppingExternal;
use App\Services\PembayaranDroppingExternalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PembayaranDroppingExternalController extends Controller
{
    public function __construct(
        protected PembayaranDroppingExternalService $service,
    ) {
    }

    public function index(Request $request)
    {
        $data = $this->service->paginate([
            'search'   => $request->get('search'),
            'dari'     => $request->get('dari'),
            'sampai'   => $request->get('sampai'),
            'per_page' => $request->get('per_page', 10),
        ]);

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return view('app.finance.kasir.pembayaran_dropping_external.index', [
            'pembayarans' => $data,
            'filters'     => $request->only(['search', 'dari', 'sampai']),
        ]);
    }

    public function create()
    {
        return view('app.finance.kasir.pembayaran_dropping_external.form', [
            'pembayaran' => new PembayaranDroppingExternal([
                'tanggal_bayar' => now(),
                'metode_bayar'  => 'tunai',
                'jenis_biaya'   => 'DROPPING',
                'kode_kasir'    => auth()->user()?->kode ?? 'ADM',
                'nama_kasir'    => auth()->user()?->name ?? 'Administrator',
            ]),
            'scan'       => null,
            'isEdit'     => false,
        ]);
    }

    /**
     * AJAX: dipanggil saat kasir menekan tombol "scan" / Enter di field Nomor Kirim.
     */
    public function cariKiriman(Request $request): JsonResponse
{
    $request->validate(['nomor_kirim' => ['required', 'string']]);

    try {
        $hasil = $this->service->cariPengiriman($request->string('nomor_kirim'));
    } catch (ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'errors'  => $e->errors(),
        ], 422);
    }

    return response()->json([
        'success' => true,
        'data'    => [
            'pengiriman_id'    => $hasil['pengiriman']->id,
            'nomor_kirim'      => $hasil['pengiriman']->nomor_pengiriman,
            'tanggal_kirim'    => optional($hasil['pengiriman']->tanggal_kirim)->format('d/m/Y'),
            'institusi_tujuan' => $hasil['pengiriman']->institusi_tujuan,
            'jenis_biaya'      => $hasil['jenis_biaya'],
            'items'            => $hasil['items'],
            'harus_dibayar'    => $hasil['harus_dibayar'],
        ],
    ]);
}
    public function store(PembayaranDroppingExternalRequest $request)
    {
        $pembayaran = $this->service->store($request->validated());

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $pembayaran], 201);
        }

        return redirect()
            ->route('finance.pembayaran_dropping_external.index')
            ->with('success', "Pembayaran dropping {$pembayaran->nomor_kirim} berhasil disimpan.");
    }

   public function edit(PembayaranDroppingExternal $pembayaran_dropping_external)
{
    $pembayaran_dropping_external->load('pengiriman.details');

    $hasil = $this->service->buildItemsForPengiriman($pembayaran_dropping_external->pengiriman);

    return view('app.finance.kasir.pembayaran_dropping_external.form', [
        'pembayaran' => $pembayaran_dropping_external,
        'scan'       => ['items' => $hasil['items'], 'jenis_biaya' => $hasil['jenis_biaya']],
        'isEdit'     => true,
    ]);
}

    public function update(PembayaranDroppingExternalRequest $request, PembayaranDroppingExternal $pembayaran_dropping_external)
    {
        $pembayaran = $this->service->update($pembayaran_dropping_external->id, $request->validated());

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $pembayaran]);
        }

        return redirect()
            ->route('finance.pembayaran_dropping_external.index')
            ->with('success', "Pembayaran dropping {$pembayaran->nomor_kirim} berhasil diperbarui.");
    }

    public function destroy(PembayaranDroppingExternal $pembayaran_dropping_external)
    {
        $this->service->delete($pembayaran_dropping_external->id);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('finance.pembayaran_dropping_external.index')
            ->with('success', 'Data pembayaran berhasil dihapus.');
    }

    public function show(PembayaranDroppingExternal $pembayaran_dropping_external)
    {
        return redirect()->route('finance.pembayaran_dropping_external.edit', $pembayaran_dropping_external);
    }
}
