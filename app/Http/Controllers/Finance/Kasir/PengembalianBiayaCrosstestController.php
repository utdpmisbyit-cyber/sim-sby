<?php

namespace App\Http\Controllers\Finance\Kasir;

use App\Http\Controllers\Controller;
use App\Models\PengembalianBiayaCrosstest;
use App\Services\PengembalianBiayaCrosstestService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PengembalianBiayaCrosstestController extends Controller
{
    public function __construct(protected PengembalianBiayaCrosstestService $service)
    {
    }

    public function index(Request $request)
    {
        $query = PengembalianBiayaCrosstest::query()->with('fpup');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('no_retur', 'like', "%{$q}%")
                  ->orWhere('no_fpup', 'like', "%{$q}%")
                  ->orWhere('nama_pasien', 'like', "%{$q}%")
                  ->orWhere('nama_rs', 'like', "%{$q}%");
            });
        }

        if ($request->filled('tgl_dari')) {
            $query->whereDate('tgl_retur', '>=', $request->tgl_dari);
        }

        if ($request->filled('tgl_sampai')) {
            $query->whereDate('tgl_retur', '<=', $request->tgl_sampai);
        }

        $data = $query->orderByDesc('id')->paginate(15)->withQueryString();

        return view('app.finance.kasir.pengembalian_biaya_crosstest.index', compact('data'));
    }

    public function create()
    {
        return view('app.finance.kasir.pengembalian_biaya_crosstest.form');
    }

    public function edit(PengembalianBiayaCrosstest $pengembalianBiayaCrosstest)
    {
        $pengembalianBiayaCrosstest->load('details');

        return view('app.finance.kasir.pengembalian_biaya_crosstest.form', [
            'pengembalian' => $pengembalianBiayaCrosstest,
        ]);
    }

    public function nextNoRetur()
    {
        return response()->json(['no_retur' => $this->service->generateNoRetur()]);
    }

    public function scanFpup(Request $request)
    {
        $request->validate(['no_fpup' => 'required|string']);

        $result = $this->service->scanFpup($request->no_fpup);

        if (! $result) {
            return response()->json(['message' => 'No FPUP tidak ditemukan.'], 404);
        }

        return response()->json($result);
    }

    public function jenisBiayaList(Request $request)
    {
        return response()->json(
            $this->service->jenisBiayaOptions($request->jenis_rs)
        );
    }

    public function hargaSatuan(Request $request)
    {
        $request->validate(['service_cost_id' => 'required|integer']);

        return response()->json([
            'harga' => $this->service->hargaSatuan((int) $request->service_cost_id),
        ]);
    }

    public function searchKasir(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        if ($q === '') {
            return response()->json([]);
        }

        return response()->json($this->service->cariKasir($q));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_fpup'                           => 'required|string',
            'permintaan_fpup_id'                => 'nullable|integer',
            'kode_kasir'                         => 'nullable|string|max:20',
            'nama_kasir'                         => 'nullable|string|max:100',
            'no_nota'                            => 'nullable|string|max:30',
            'keterangan'                         => 'nullable|string',
            'total_retur'                        => 'required|numeric|min:0',
            'jenis_biaya_id'                     => 'nullable|integer',
            'kode_service_cost'                  => 'nullable|string',
            'items'                              => 'required|array|min:1',
            'items.*.permintaan_fpup_detail_id'  => 'nullable|integer',
            'items.*.nama_os'                    => 'nullable|string',
            'items.*.jns_darah'                  => 'nullable|string',
            'items.*.gol_darah'                  => 'nullable|string',
            'items.*.rhesus'                     => 'nullable|string',
            'items.*.jumlah'                      => 'required|integer|min:1',
            'items.*.cc'                          => 'nullable|integer',
            'items.*.harga_satuan'                => 'required|numeric|min:0',
        ]);

        $pengembalian = $this->service->store($validated);

        return response()->json([
            'message' => 'Retur biaya cross test berhasil disimpan.',
            'data'    => $pengembalian->load('details'),
        ], 201);
    }

    public function show(PengembalianBiayaCrosstest $pengembalianBiayaCrosstest)
    {
        return response()->json($pengembalianBiayaCrosstest->load('details', 'fpup'));
    }

    public function update(Request $request, PengembalianBiayaCrosstest $pengembalianBiayaCrosstest)
    {
        $validated = $request->validate([
            'kode_kasir'                         => 'nullable|string|max:20',
            'nama_kasir'                          => 'nullable|string|max:100',
            'no_nota'                             => 'nullable|string|max:30',
            'keterangan'                          => 'nullable|string',
            'total_retur'                         => 'required|numeric|min:0',
            'jenis_biaya_id'                      => 'nullable|integer',
            'kode_service_cost'                   => 'nullable|string',
            'items'                               => 'required|array|min:1',
            'items.*.permintaan_fpup_detail_id'   => 'nullable|integer',
            'items.*.nama_os'                     => 'nullable|string',
            'items.*.jns_darah'                   => 'nullable|string',
            'items.*.gol_darah'                   => 'nullable|string',
            'items.*.rhesus'                      => 'nullable|string',
            'items.*.jumlah'                      => 'required|integer|min:1',
            'items.*.cc'                          => 'nullable|integer',
            'items.*.harga_satuan'                => 'required|numeric|min:0',
        ]);

        $pengembalian = $this->service->update($pengembalianBiayaCrosstest, $validated);

        return response()->json([
            'message' => 'Data retur berhasil diperbarui.',
            'data'    => $pengembalian->load('details'),
        ]);
    }

    public function updateStatus(Request $request, PengembalianBiayaCrosstest $pengembalianBiayaCrosstest)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['baru', 'disimpan', 'batal'])],
        ]);

        $pengembalianBiayaCrosstest->update($validated);

        return response()->json([
            'message' => 'Status diperbarui.',
            'data'    => $pengembalianBiayaCrosstest,
        ]);
    }

    public function destroy(PengembalianBiayaCrosstest $pengembalianBiayaCrosstest)
    {
        $pengembalianBiayaCrosstest->delete();

        return response()->json(['message' => 'Data retur dihapus.']);
    }

    public function print(PengembalianBiayaCrosstest $pengembalianBiayaCrosstest)
    {
        $pengembalianBiayaCrosstest->load('details', 'fpup');

        return view('app.finance.kasir.pengembalian_biaya_crosstest.cetak', compact('pengembalianBiayaCrosstest'));
    }
}