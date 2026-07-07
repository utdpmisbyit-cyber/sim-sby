<?php

namespace App\Http\Controllers\Aftap;

use App\Http\Controllers\Controller;
use App\Models\PengembalianKantong;
use App\Models\PengembalianKantongDetail;
use App\Models\TipeKantong;
use App\Models\AsalDarah;
use App\Services\PengembalianKantongService;
use App\Services\PengembalianKantongDetailService;
use App\Services\PengirimanSampleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PengembalianKantongController extends Controller
{
    protected PengembalianKantongService       $service;
    protected PengembalianKantongDetailService $detailService;
    protected PengirimanSampleService          $sampleService;

    public function __construct(
        PengembalianKantongService       $service,
        PengembalianKantongDetailService $detailService,
        PengirimanSampleService          $sampleService
    ) {
        $this->service       = $service;
        $this->detailService = $detailService;
        $this->sampleService = $sampleService;
    }

    public function index(Request $request)
    {
        $params = $request->only(['no_kembali', 'no_kantong', 'kondisi', 'tgl_kembali', 'asal_darah', 'per_page']);
        $data   = $this->service->search($params);

        return view('app.aftap.pengembalian_kantong.index', compact('data', 'params'));
    }

    public function create()
    {
        $no_kembali   = $this->service->generateNoKembali();
        $tipe_kantong = TipeKantong::orderBy('nama')->get();
        $asal_darah   = AsalDarah::orderBy('nama')->get();

        return view('app.aftap.pengembalian_kantong.form', compact('no_kembali', 'tipe_kantong', 'asal_darah'));
    }

    /**
     * ──────────────────────────────────────────────────────────
     * SCAN KANTONG (untuk Pengembalian)
     * - Kantong dicari di stok_kantong_penerimaan_detail (data stok fisik
     *   hasil alur Penerimaan) — BUKAN dari StokKantong/stok_kantong_masuk
     *   (tabel batch kontainer kosong yang tidak melacak per-nomor kantong).
     * - HANYA kantong dengan status_kirim = 'tersedia' (BELUM pernah
     *   ditransaksikan ke Pengiriman Sample / Serologi) yang boleh
     *   dikembalikan. Kalau sudah 'sample' atau 'serologi', ditolak.
     * - Data donor/asal darah (opsional, untuk info tambahan) diambil
     *   dari data aftap — BUKAN dari Pengiriman Sample, karena kantong
     *   ini justru belum pernah masuk ke transaksi sample.
     * - Sisa stok dihitung OTOMATIS dari kombinasi merk+jenis+ukuran:
     *   jumlah kantong lain dgn spesifikasi sama yang masih 'tersedia',
     *   dikurangi total yang sudah tercatat di transaksi pengembalian lain.
     * ──────────────────────────────────────────────────────────
     */
    public function scanKantong(Request $request)
    {
        $request->validate(['no_kantong' => 'required|string']);

        $noKantong = trim($request->no_kantong);

        // ── Kantong harus sudah tercatat diterima gudang ──
        $stok = $this->service->findKantongFisik($noKantong);

        if (! $stok) {
            return response()->json([
                'message' => "Kantong {$noKantong} tidak ditemukan di data stok penerimaan.",
            ], 404);
        }

        // ── Hanya kantong yang BELUM dikirim ke sample/serologi yang boleh dikembalikan ──
        if ($stok->status_kirim !== 'tersedia') {
            $label = $stok->status_kirim === 'sample'
                ? 'sudah dikirim ke Pengiriman Sample'
                : 'sudah dikirim ke Serologi';

            return response()->json([
                'message' => "Kantong {$noKantong} {$label}, tidak dapat dikembalikan.",
            ], 422);
        }

        // ── Data donor/asal darah (opsional) dari data aftap ──
        $aftap = null;
        try {
            $aftap = $this->sampleService->getKantongByScan($noKantong);
        } catch (\Throwable $e) {
            // Data aftap tidak wajib ada untuk proses pengembalian; abaikan jika tidak ketemu.
        }

        // ── Sisa stok dihitung dari kombinasi merk+jenis+ukuran, bukan
        //    per no_kantong tunggal ──
        $sisaInfo = $this->service->getSisaStok($stok->merk, $stok->jenis, $stok->ukuran);

        return response()->json([
            'no_kantong'      => $stok->no_kantong,
            'stok_kantong_id' => $stok->id,
            'merk'            => $stok->merk   ?? '-',
            'jenis'           => $stok->jenis  ?? '-',
            'tipe'            => $stok->tipe   ?? '-',
            'ukuran'          => $stok->ukuran ?? '-',
            'no_lot'          => $stok->no_lot ?? '-',
            'status_kirim'    => $stok->status_kirim,

            // ── Stok riil hasil perhitungan otomatis ──
            'jumlah'                   => $sisaInfo['sisa'],
            'jumlah_tersedia_saat_ini' => $sisaInfo['jumlah_tersedia_saat_ini'],
            'sudah_dikembalikan'       => $sisaInfo['sudah_dikembalikan'],

            // ── Data tambahan dari aftap (bisa null, tidak wajib) ──
            'asal_darah_id'   => $aftap->asal_darah_id ?? null,
            'asal_darah_nama' => $aftap->asal_darah    ?? null,
            'no_donor'        => $aftap->no_donor      ?? null,
            'nama_donor'      => $aftap->nama_donor    ?? null,
            'gol_darah'       => $aftap->gol_darah     ?? null,
            'rhesus'          => $aftap->rhesus        ?? null,
            'tanggal_aftap'   => $aftap && $aftap->tanggal_aftap
                ? \Carbon\Carbon::parse($aftap->tanggal_aftap)->format('d/m/Y')
                : null,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_kembali'                  => 'required|date',
            'no_kantong'                   => 'required|string|max:100',
            'stok_kantong_id'              => 'required|integer|exists:stok_kantong_penerimaan_detail,id',
            'asal_darah_id'                => 'nullable|integer|exists:asal_darah,id',
            'kondisi'                      => 'required|in:baik,rusak',
            'keterangan'                   => 'nullable|string|max:255',
            'details'                      => 'nullable|array',
            'details.*.tipe_kantong_id'    => 'nullable|integer|exists:tipe_kantong,id',
            'details.*.jumlah'             => 'required_with:details|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($request) {

                $totalJumlahBaru = collect($request->details ?? [])->sum(function ($d) {
                    return (int) ($d['jumlah'] ?? 0);
                });

                // ── Validasi otomatis: total tidak boleh melebihi sisa stok riil ──
                // dihitung dari kombinasi merk+jenis+ukuran yang dikirim di form.
                if ($totalJumlahBaru > 0) {
                    $this->service->assertJumlahValid(
                        $request->merk,
                        $request->jenis,
                        $request->ukuran,
                        $totalJumlahBaru
                    );
                }

                $pengembalian = PengembalianKantong::create([
                    'no_kembali'      => $this->service->generateNoKembali(),
                    'tgl_kembali'     => $request->tgl_kembali,
                    'no_kantong'      => $request->no_kantong,
                    'stok_kantong_id' => $request->stok_kantong_id,
                    'asal_darah_id'   => $request->asal_darah_id,
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
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['details' => $e->getMessage()]);
        }

        return redirect()
            ->route('aftap.pengembalian_kantong.index')
            ->with('success', 'Pengembalian kantong berhasil disimpan.');
    }

    public function show(Request $request, $id)
    {
        $pengembalian = PengembalianKantong::with(['stokKantong', 'asalDarah', 'details.tipe_kantong'])
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
        $asal_darah   = AsalDarah::orderBy('nama')->get();

        // ── Sisa stok otomatis, dengan record ini sendiri dikecualikan
        //    dari perhitungan "sudah dikembalikan" ──
        $sisaInfo = $this->service->getSisaStok(
            $pengembalian->merk,
            $pengembalian->jenis,
            $pengembalian->ukuran,
            $pengembalian->id
        );

        return view('aftap.pengembalian_kantong.form', compact(
            'pengembalian', 'tipe_kantong', 'asal_darah', 'sisaInfo'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tgl_kembali'                  => 'required|date',
            'no_kantong'                   => 'required|string|max:100',
            'stok_kantong_id'              => 'required|integer|exists:stok_kantong_penerimaan_detail,id',
            'asal_darah_id'                => 'nullable|integer|exists:asal_darah,id',
            'kondisi'                      => 'required|in:baik,rusak',
            'keterangan'                   => 'nullable|string|max:255',
            'details'                      => 'nullable|array',
            'details.*.tipe_kantong_id'    => 'nullable|integer|exists:tipe_kantong,id',
            'details.*.jumlah'             => 'required_with:details|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $pengembalian = PengembalianKantong::findOrFail($id);

                $totalJumlahBaru = collect($request->details ?? [])->sum(function ($d) {
                    return (int) ($d['jumlah'] ?? 0);
                });

                // ── Validasi otomatis, record ini sendiri dikecualikan ──
                if ($totalJumlahBaru > 0) {
                    $this->service->assertJumlahValid(
                        $request->merk,
                        $request->jenis,
                        $request->ukuran,
                        $totalJumlahBaru,
                        $pengembalian->id
                    );
                }

                $pengembalian->update([
                    'tgl_kembali'     => $request->tgl_kembali,
                    'no_kantong'      => $request->no_kantong,
                    'stok_kantong_id' => $request->stok_kantong_id,
                    'asal_darah_id'   => $request->asal_darah_id,
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
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['details' => $e->getMessage()]);
        }

        return redirect()
            ->route('aftap.pengembalian_kantong.index')
            ->with('success', 'Pengembalian kantong berhasil diperbarui.');
    }

    /**
     * Endpoint AJAX untuk search dropdown Asal Darah (pola registerSearchDropdown()
     * yang sudah dipakai di modul Pendaftaran Donor).
     * Response format: { results: [{ id, code, text }] }
     */
    public function selectAsalDarah(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $items = AsalDarah::query()
            ->when($q !== '', fn ($query) => $query->where('nama', 'like', '%' . $q . '%'))
            ->orderBy('nama')
            ->limit(30)
            ->get()
            ->map(fn ($a) => [
                'id'   => $a->id,
                'code' => str_pad((string) $a->id, 4, '0', STR_PAD_LEFT),
                'text' => $a->nama,
            ]);

        return response()->json(['results' => $items]);
    }

    public function destroy($id)
    {
        // Hapus dilakukan biasa. Karena sisa stok dihitung otomatis
        // (dinamis dari sum PengembalianKantongDetail), begitu record
        // ini hilang, sisa stok batch terkait otomatis "naik lagi" —
        // tidak perlu proses revert manual apa pun.
        PengembalianKantong::findOrFail($id)->delete();

        return redirect()
            ->route('aftap.pengembalian_kantong.index')
            ->with('success', 'Data berhasil dihapus.');
    }
}