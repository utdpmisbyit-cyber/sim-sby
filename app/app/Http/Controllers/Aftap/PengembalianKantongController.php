<?php

namespace App\Http\Controllers\Aftap;

use App\Http\Controllers\Controller;
use App\Models\PengembalianKantong;
use App\Models\PengembalianKantongDetail;
use App\Models\StokKantong;
use App\Models\TipeKantong;
use App\Services\PengembalianKantongService;
use App\Services\PengembalianKantongDetailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PengembalianKantongController extends Controller
{
    protected PengembalianKantongService       $service;
    protected PengembalianKantongDetailService $detailService;

    public function __construct(
        PengembalianKantongService       $service,
        PengembalianKantongDetailService $detailService
    ) {
        $this->service       = $service;
        $this->detailService = $detailService;
    }

    public function index(Request $request)
    {
        $params = $request->only(['no_kembali', 'no_kantong', 'kondisi', 'tgl_kembali', 'per_page']);
        $data   = $this->service->search($params);

        return view('app.aftap.pengembalian_kantong.index', compact('data', 'params'));
    }

    public function create()
    {
        $no_kembali   = $this->service->generateNoKembali();
        $tipe_kantong = TipeKantong::orderBy('nama')->get();

        return view('app.aftap.pengembalian_kantong.form', compact('no_kembali', 'tipe_kantong'));
    }

    public function scanKantong(Request $request)
    {
        $request->validate(['no_kantong' => 'required|string']);

        $stok = StokKantong::where('no_kantong', $request->no_kantong)->first();

        if (! $stok) {
            return response()->json(['message' => 'Nomor kantong tidak ditemukan.'], 404);
        }

        // Kembalikan juga jumlah stok saat ini supaya form bisa auto-populate
        return response()->json([
            'no_kantong'      => $stok->no_kantong,
            'stok_kantong_id' => $stok->id,
            'merk'            => $stok->merk   ?? '-',
            'jenis'           => $stok->jenis  ?? '-',
            'tipe'            => $stok->tipe   ?? '-',
            'ukuran'          => $stok->ukuran ?? '-',
            'jumlah'          => $stok->jumlah ?? 0,   // ← stok saat ini
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_kembali'                  => 'required|date',
            'no_kantong'                   => 'required|string|max:100',
            'stok_kantong_id'              => 'required|integer|exists:stok_kantong_masuk,id',
            'kondisi'                      => 'required|in:baik,rusak',
            'keterangan'                   => 'nullable|string|max:255',
            'details'                      => 'nullable|array',
            'details.*.tipe_kantong_id'    => 'nullable|integer|exists:tipe_kantong,id',
            'details.*.jumlah'             => 'required_with:details|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $pengembalian = PengembalianKantong::create([
                'no_kembali'      => $this->service->generateNoKembali(),
                'tgl_kembali'     => $request->tgl_kembali,
                'no_kantong'      => $request->no_kantong,
                'stok_kantong_id' => $request->stok_kantong_id,
                'merk'            => $request->merk,
                'jenis'           => $request->jenis,
                'tipe'            => $request->tipe,
                'ukuran'          => $request->ukuran,
                'kondisi'         => $request->kondisi,
                'keterangan'      => $request->keterangan,
                'created_by'      => Auth::id(),
            ]);

            foreach ((array) $request->details as $detail) {
                if (empty($detail['jumlah'])) continue;
                PengembalianKantongDetail::create([
                    'pengembalian_kantong_id' => $pengembalian->id,
                    'tipe_kantong_id'         => $detail['tipe_kantong_id'] ?? null,
                    'jumlah'                  => $detail['jumlah'],
                    'flag'                    => $detail['flag'] ?? 0,
                ]);
            }
        });

        return redirect()
            ->route('app.aftap.pengembalian_kantong.index') 
            ->with('success', 'Pengembalian kantong berhasil disimpan.');
    }

    public function show(Request $request, $id)
    {
        $pengembalian = PengembalianKantong::with(['stokKantong', 'details.tipe_kantong'])
            ->findOrFail($id);

        if (! $request->ajax()) {
            return redirect()->route('app.aftap.pengembalian_kantong.edit', $id); 
        }

        return response()->json(array_merge(
            $pengembalian->toArray(),
            ['tgl_kembali_fmt' => $pengembalian->tgl_kembali->format('d/m/Y')]
        ));
    }

    public function edit($id)
    {
        $pengembalian = PengembalianKantong::with('details.tipe_kantong')->findOrFail($id);
        $tipe_kantong = TipeKantong::orderBy('nama')->get();

        return view('app.aftap.pengembalian_kantong.form', compact('pengembalian', 'tipe_kantong'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tgl_kembali'                  => 'required|date',
            'no_kantong'                   => 'required|string|max:100',
            'stok_kantong_id'              => 'required|integer|exists:stok_kantong_masuk,id',
            'kondisi'                      => 'required|in:baik,rusak',
            'keterangan'                   => 'nullable|string|max:255',
            'details'                      => 'nullable|array',
            'details.*.tipe_kantong_id'    => 'nullable|integer|exists:tipe_kantong,id',
            'details.*.jumlah'             => 'required_with:details|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $id) {
            $pengembalian = PengembalianKantong::findOrFail($id);

            $pengembalian->update([
                'tgl_kembali'     => $request->tgl_kembali,
                'no_kantong'      => $request->no_kantong,
                'stok_kantong_id' => $request->stok_kantong_id,
                'merk'            => $request->merk,
                'jenis'           => $request->jenis,
                'tipe'            => $request->tipe,
                'ukuran'          => $request->ukuran,
                'kondisi'         => $request->kondisi,
                'keterangan'      => $request->keterangan,
            ]);

            $pengembalian->details()->delete();

            foreach ((array) $request->details as $detail) {
                if (empty($detail['jumlah'])) continue;
                PengembalianKantongDetail::create([
                    'pengembalian_kantong_id' => $pengembalian->id,
                    'tipe_kantong_id'         => $detail['tipe_kantong_id'] ?? null,
                    'jumlah'                  => $detail['jumlah'],
                    'flag'                    => $detail['flag'] ?? 0,
                ]);
            }
        });

        return redirect()
            ->route('app.aftap.pengembalian_kantong.index')  
            ->with('success', 'Pengembalian kantong berhasil diperbarui.');
    }

    public function destroy($id)
    {
        PengembalianKantong::findOrFail($id)->delete();

        return redirect()
            ->route('app.aftap.pengembalian_kantong.index')   
            ->with('success', 'Data berhasil dihapus.');
    }
}