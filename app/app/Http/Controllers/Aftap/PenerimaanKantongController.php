<?php

namespace App\Http\Controllers\Aftap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenerimaanKantongController extends Controller
{
    protected string $tablePenerimaan       = 'stok_kantong_penerimaan';
    protected string $tablePenerimaanDetail = 'stok_kantong_penerimaan_detail';

    /**
     * ──────────────────────────────────────────────────────────
     * INDEX
     * - Tanpa parameter  → render halaman (Blade)
     * - ?mode=history    → JSON daftar riwayat penerimaan
     * - ?mode=detail&id= → JSON detail 1 transaksi penerimaan
     * ──────────────────────────────────────────────────────────
     */
    public function index(Request $request)
    {
        if ($request->get('mode') === 'history') {
            return $this->historyJson($request);
        }

        if ($request->get('mode') === 'detail') {
            return $this->detailJson($request);
        }

        return view('app.aftap.penerimaan_kantong.index', [
            'no_transaksi' => $this->generateNoTransaksi(),
        ]);
    }

    /**
     * ──────────────────────────────────────────────────────────
     * NEXT NO TRANSAKSI (JSON)
     * Dipanggil dari JS setelah simpan/update/batal-edit supaya
     * form siap untuk transaksi berikutnya tanpa reload halaman.
     * ──────────────────────────────────────────────────────────
     */
    public function nextNoTransaksi()
    {
        return response()->json(['no_transaksi' => $this->generateNoTransaksi()]);
    }

    /**
     * ──────────────────────────────────────────────────────────
     * RIWAYAT (JSON)
     * ──────────────────────────────────────────────────────────
     */
    protected function historyJson(Request $request)
    {
        $dari    = $request->get('dari');
        $sampai  = $request->get('sampai');
        $keyword = $request->get('keyword');
        $page    = max(1, (int) $request->get('page', 1));
        $per     = max(1, (int) $request->get('per', 10));

        $detail = $this->tablePenerimaanDetail;

        $base = DB::table($this->tablePenerimaan . ' as h')
            ->whereNull('h.deleted_at')
            ->when($dari,    fn ($q) => $q->whereDate('h.tanggal', '>=', $dari))
            ->when($sampai,  fn ($q) => $q->whereDate('h.tanggal', '<=', $sampai))
            ->when($keyword, function ($q) use ($keyword) {
                $q->where(function ($qq) use ($keyword) {
                    $qq->where('h.no_transaksi', 'like', "%{$keyword}%")
                       ->orWhere('h.no_keluar', 'like', "%{$keyword}%")
                       ->orWhere('h.kode_permintaan', 'like', "%{$keyword}%");
                });
            });

        $total = (clone $base)->count();

        $rows = $base
            ->select([
                'h.id',
                'h.no_transaksi',
                'h.tanggal',
                'h.kode_permintaan',
                'h.no_keluar',
                DB::raw("(SELECT COUNT(*) FROM {$detail} d WHERE d.penerimaan_id = h.id) as detail_count"),
                DB::raw("(SELECT COUNT(*) FROM {$detail} d WHERE d.penerimaan_id = h.id AND d.status_kirim = 'sample') as sudah_sample"),
                DB::raw("(SELECT COUNT(*) FROM {$detail} d WHERE d.penerimaan_id = h.id AND d.status_kirim = 'serologi') as sudah_serologi"),
                DB::raw("(SELECT COUNT(*) FROM {$detail} d WHERE d.penerimaan_id = h.id AND d.status_kirim = 'tersedia') as sisa_stok"),
            ])
            ->orderByDesc('h.id')
            ->forPage($page, $per)
            ->get()
            ->map(function ($row) {
                $row->tanggal = date('Y-m-d', strtotime($row->tanggal));
                return $row;
            });

        return response()->json([
            'total' => $total,
            'data'  => $rows,
        ]);
    }

    /**
     * ──────────────────────────────────────────────────────────
     * DETAIL 1 TRANSAKSI (JSON) — untuk modal "Lihat detail"
     * ──────────────────────────────────────────────────────────
     */
    protected function detailJson(Request $request)
    {
        $id = $request->get('id');

        $rows = DB::table($this->tablePenerimaanDetail)
            ->where('penerimaan_id', $id)
            ->orderBy('id')
            ->get();

        return response()->json(['data' => $rows]);
    }

    /**
     * ──────────────────────────────────────────────────────────
     * EDIT — ambil data 1 transaksi untuk diisi ke form (mode edit)
     * ──────────────────────────────────────────────────────────
     */
    public function edit($id)
    {
        $header = DB::table($this->tablePenerimaan)
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (!$header) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $items = DB::table($this->tablePenerimaanDetail)
            ->where('penerimaan_id', $id)
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'              => $header->id,
                'no_transaksi'    => $header->no_transaksi,
                'tanggal'         => date('Y-m-d', strtotime($header->tanggal)),
                'no_keluar'       => $header->no_keluar,
                'kode_permintaan' => $header->kode_permintaan,
                'items'           => $items,
            ],
        ]);
    }

    /**
     * ──────────────────────────────────────────────────────────
     * UPDATE — simpan perubahan 1 transaksi penerimaan
     * - no_transaksi TIDAK berubah (identitas transaksi)
     * - kantong yang dihapus dari daftar → baris detail dihapus
     *   (kantong itu jadi bisa diterima ulang di transaksi lain)
     * - kantong baru yang ditambahkan → divalidasi belum diterima
     *   di transaksi LAIN, lalu di-insert
     * - kantong yang tetap ada → dibiarkan (status_kirim/history-nya
     *   tidak direset, supaya progres FPD sample/serologi tidak hilang)
     * ──────────────────────────────────────────────────────────
     */
    public function update(Request $request, $id)
    {
        $header = DB::table($this->tablePenerimaan)
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (!$header) {
            return response()->json(['status' => false, 'msg' => 'Data tidak ditemukan'], 404);
        }

        $data = $request->validate([
            'tanggal'            => 'required|date',
            'kode'               => 'nullable|string',
            'no_keluar'          => 'required|string',
            'items'              => 'required|array|min:1',
            'items.*.no_kantong' => 'required|string',
            'items.*.merk'       => 'nullable|string',
            'items.*.jenis'      => 'nullable|string',
            'items.*.ukuran'     => 'nullable|string',
            'items.*.no_lot'     => 'nullable|string',
        ]);

        return DB::transaction(function () use ($data, $id) {

            $existing   = DB::table($this->tablePenerimaanDetail)
                ->where('penerimaan_id', $id)
                ->get()
                ->keyBy('no_kantong');

            $newKantong = collect($data['items'])->pluck('no_kantong');

            // ── Hapus kantong yang tidak lagi ada di daftar baru ──
            $toRemove = $existing->keys()->diff($newKantong);
            if ($toRemove->isNotEmpty()) {
                DB::table($this->tablePenerimaanDetail)
                    ->where('penerimaan_id', $id)
                    ->whereIn('no_kantong', $toRemove)
                    ->delete();
            }

            // ── Kantong baru (belum ada sebelumnya di transaksi ini) ──
            $toAdd = collect($data['items'])->reject(fn ($it) => $existing->has($it['no_kantong']));

            if ($toAdd->isNotEmpty()) {
                // pastikan tidak sedang tercatat diterima di transaksi LAIN
                $dupe = DB::table($this->tablePenerimaanDetail)
                    ->where('penerimaan_id', '!=', $id)
                    ->whereIn('no_kantong', $toAdd->pluck('no_kantong'))
                    ->pluck('no_kantong');

                if ($dupe->isNotEmpty()) {
                    return response()->json([
                        'status' => false,
                        'msg'    => 'Kantong berikut sudah diterima di transaksi lain: ' . $dupe->implode(', '),
                    ], 422);
                }

                $rows = $toAdd->map(fn ($it) => [
                    'penerimaan_id' => $id,
                    'no_kantong'    => $it['no_kantong'],
                    'merk'          => $it['merk']   ?? null,
                    'jenis'         => $it['jenis']  ?? null,
                    'ukuran'        => $it['ukuran'] ?? null,
                    'no_lot'        => $it['no_lot'] ?? null,
                    'status_kirim'  => 'tersedia',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ])->all();

                DB::table($this->tablePenerimaanDetail)->insert($rows);
            }

            $permintaanId = null;
            if (!empty($data['kode'])) {
                $permintaanId = DB::table('permintaan_kantong')->where('nomor', $data['kode'])->value('id');
            }

            DB::table($this->tablePenerimaan)->where('id', $id)->update([
                'tanggal'               => $data['tanggal'],
                'no_keluar'             => $data['no_keluar'],
                'kode_permintaan'       => $data['kode'] ?? null,
                'permintaan_kantong_id' => $permintaanId,
                'updated_at'            => now(),
            ]);

            return response()->json(['status' => true, 'msg' => 'Penerimaan berhasil diperbarui']);
        });
    }

    /**
     * ──────────────────────────────────────────────────────────
     * DESTROY — hapus 1 transaksi penerimaan (soft delete header,
     * hard delete detail supaya kantongnya bisa diterima ulang)
     * ──────────────────────────────────────────────────────────
     */
    public function destroy($id)
    {
        $header = DB::table($this->tablePenerimaan)
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (!$header) {
            return response()->json(['status' => false, 'msg' => 'Data tidak ditemukan'], 404);
        }

        DB::transaction(function () use ($id) {
            DB::table($this->tablePenerimaanDetail)->where('penerimaan_id', $id)->delete();
            DB::table($this->tablePenerimaan)->where('id', $id)->update(['deleted_at' => now()]);
        });

        return response()->json(['status' => true, 'msg' => 'Penerimaan berhasil dihapus']);
    }

    /**
     * ──────────────────────────────────────────────────────────
     * SCAN — verifikasi 1 no_kantong saat proses penerimaan
     * ──────────────────────────────────────────────────────────
     */
    public function scan(Request $request)
    {
        $request->validate(['no_kantong' => 'required|string']);
        $noKantong = trim($request->no_kantong);

        $stok = DB::table('stok_kantong_keluar')
            ->where('no_kantong', $noKantong)
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->first();

        if (!$stok) {
            return response()->json([
                'status' => false,
                'msg'    => "Kantong {$noKantong} tidak ditemukan di data pengeluaran gudang",
            ]);
        }

        $sudahDiterima = DB::table($this->tablePenerimaanDetail)
            ->where('no_kantong', $noKantong)
            ->exists();

        if ($sudahDiterima) {
            return response()->json([
                'status' => false,
                'msg'    => "Kantong {$noKantong} sudah pernah diterima sebelumnya",
            ]);
        }

        return response()->json([
            'status' => true,
            'data'   => [
                'no_kantong' => $stok->no_kantong,
                'merk'       => $stok->merk,
                'jenis'      => $stok->jenis,
                'ukuran'     => $stok->ukuran,
                'no_lot'     => $stok->no_lot,
            ],
        ]);
    }

    /**
     * ──────────────────────────────────────────────────────────
     * STORE — simpan hasil penerimaan (transaksi baru)
     * ──────────────────────────────────────────────────────────
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'no_transaksi'       => 'required|string|max:30',
            'tanggal'            => 'required|date',
            'kode'               => 'nullable|string',
            'no_keluar'          => 'required|string',
            'items'              => 'required|array|min:1',
            'items.*.no_kantong' => 'required|string',
            'items.*.merk'       => 'nullable|string',
            'items.*.jenis'      => 'nullable|string',
            'items.*.ukuran'     => 'nullable|string',
            'items.*.no_lot'     => 'nullable|string',
        ]);

        return DB::transaction(function () use ($data) {

            if (DB::table($this->tablePenerimaan)->where('no_transaksi', $data['no_transaksi'])->exists()) {
                return response()->json([
                    'status' => false,
                    'msg'    => "No transaksi {$data['no_transaksi']} sudah pernah dipakai",
                ], 422);
            }

            $noKantongList = array_column($data['items'], 'no_kantong');
            $sudahAda = DB::table($this->tablePenerimaanDetail)
                ->whereIn('no_kantong', $noKantongList)
                ->pluck('no_kantong');

            if ($sudahAda->isNotEmpty()) {
                return response()->json([
                    'status' => false,
                    'msg'    => 'Kantong berikut sudah pernah diterima: ' . $sudahAda->implode(', '),
                ], 422);
            }

            $permintaanId = null;
            if (!empty($data['kode'])) {
                $permintaanId = DB::table('permintaan_kantong')->where('nomor', $data['kode'])->value('id');
            }

            $penerimaanId = DB::table($this->tablePenerimaan)->insertGetId([
                'no_transaksi'          => $data['no_transaksi'],
                'tanggal'               => $data['tanggal'],
                'no_keluar'             => $data['no_keluar'],
                'kode_permintaan'       => $data['kode'] ?? null,
                'permintaan_kantong_id' => $permintaanId,
                'created_by'            => auth()->id() ?? 1,
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            $rows = array_map(function ($item) use ($penerimaanId) {
                return [
                    'penerimaan_id' => $penerimaanId,
                    'no_kantong'    => $item['no_kantong'],
                    'merk'          => $item['merk']   ?? null,
                    'jenis'         => $item['jenis']  ?? null,
                    'ukuran'        => $item['ukuran'] ?? null,
                    'no_lot'        => $item['no_lot'] ?? null,
                    'status_kirim'  => 'tersedia',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }, $data['items']);

            DB::table($this->tablePenerimaanDetail)->insert($rows);

            return response()->json([
                'status' => true,
                'msg'    => 'Penerimaan berhasil disimpan',
                'id'     => $penerimaanId,
            ]);
        });
    }

    /**
     * ──────────────────────────────────────────────────────────
     * SEARCH — No. Gudang Keluar (tabel: stok_kantong_keluar)
     * ──────────────────────────────────────────────────────────
     */
    public function searchNoKeluar(Request $request)
    {
        $q = trim($request->get('q', ''));

        $rows = DB::table('stok_kantong_keluar')
            ->select('no_keluar', DB::raw('COUNT(*) as jumlah'))
            ->whereNull('deleted_at')
            ->when($q !== '', fn ($query) => $query->where('no_keluar', 'like', "%{$q}%"))
            ->groupBy('no_keluar')
            ->orderByDesc('no_keluar')
            ->limit(15)
            ->get();

        return response()->json($rows);
    }

    /**
     * ──────────────────────────────────────────────────────────
     * SEARCH — No. Permintaan (tabel: permintaan_kantong)
     * ──────────────────────────────────────────────────────────
     */
    public function searchNoPermintaan(Request $request)
    {
        $q = trim($request->get('q', ''));

        $rows = DB::table('permintaan_kantong as p')
            ->select('p.id', 'p.nomor as no_permintaan')
            ->when($q !== '', fn ($query) => $query->where('p.nomor', 'like', "%{$q}%"))
            ->orderByDesc('p.id')
            ->limit(15)
            ->get()
            ->map(function ($row) {
                $jumlahKantong = DB::table('stok_kantong_keluar')
                    ->where('permintaan_kantong_id', $row->id)
                    ->whereNull('deleted_at')
                    ->count();

                $noKeluar = DB::table('stok_kantong_keluar')
                    ->where('permintaan_kantong_id', $row->id)
                    ->whereNull('deleted_at')
                    ->orderByDesc('id')
                    ->value('no_keluar');

                return [
                    'id'             => $row->id,
                    'no_permintaan'  => $row->no_permintaan,
                    'jumlah_kantong' => $jumlahKantong,
                    'no_keluar'      => $noKeluar ?? '-',
                ];
            });

        return response()->json($rows);
    }

    /**
     * ──────────────────────────────────────────────────────────
     * GET JUMLAH KIRIM
     * ──────────────────────────────────────────────────────────
     */
    public function getJumlah(Request $request)
    {
        $request->validate(['no_keluar' => 'required|string']);

        $jumlah = DB::table('stok_kantong_keluar')
            ->where('no_keluar', $request->no_keluar)
            ->whereNull('deleted_at')
            ->count();

        return response()->json(['jumlah_kirim' => $jumlah]);
    }

    /**
     * ──────────────────────────────────────────────────────────
     * AMBIL SEMUA KANTONG BERDASARKAN NO. GUDANG KELUAR
     * (dikecualikan yang sudah pernah diterima)
     * ──────────────────────────────────────────────────────────
     */
    public function kantongByNoKeluar(Request $request)
    {
        $request->validate(['no_keluar' => 'required|string']);

        $rows = DB::table('stok_kantong_keluar as k')
            ->leftJoin('permintaan_kantong as p', 'p.id', '=', 'k.permintaan_kantong_id')
            ->where('k.no_keluar', $request->no_keluar)
            ->whereNull('k.deleted_at')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from($this->tablePenerimaanDetail . ' as d')
                  ->whereColumn('d.no_kantong', 'k.no_kantong');
            })
            ->select(
                'k.no_kantong',
                'k.merk',
                'k.jenis',
                'k.ukuran',
                'k.no_lot',
                'k.no_keluar',
                'p.nomor as no_permintaan'
            )
            ->orderBy('k.id')
            ->get();

        if ($rows->isEmpty()) {
            return response()->json([
                'status' => false,
                'msg'    => "Tidak ada kantong yang bisa diterima untuk No. Gudang Keluar \"{$request->no_keluar}\" (mungkin sudah tidak ditemukan atau semua sudah diterima)",
            ]);
        }

        return response()->json(['status' => true, 'data' => $rows]);
    }

    /**
     * ──────────────────────────────────────────────────────────
     * AMBIL SEMUA KANTONG BERDASARKAN NO. PERMINTAAN
     * (dikecualikan yang sudah pernah diterima)
     * ──────────────────────────────────────────────────────────
     */
    public function kantongByNoPermintaan(Request $request)
    {
        $request->validate(['no_permintaan' => 'required|string']);

        $permintaan = DB::table('permintaan_kantong')
            ->where('nomor', $request->no_permintaan)
            ->first();

        if (!$permintaan) {
            return response()->json([
                'status' => false,
                'msg'    => "No. Permintaan \"{$request->no_permintaan}\" tidak ditemukan",
            ]);
        }

        $rows = DB::table('stok_kantong_keluar as k')
            ->where('k.permintaan_kantong_id', $permintaan->id)
            ->whereNull('k.deleted_at')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from($this->tablePenerimaanDetail . ' as d')
                  ->whereColumn('d.no_kantong', 'k.no_kantong');
            })
            ->select('k.no_kantong', 'k.merk', 'k.jenis', 'k.ukuran', 'k.no_lot', 'k.no_keluar')
            ->orderBy('k.id')
            ->get();

        if ($rows->isEmpty()) {
            return response()->json([
                'status' => false,
                'msg'    => 'Tidak ada kantong yang bisa diterima untuk permintaan ini (mungkin belum dikeluarkan atau semua sudah diterima)',
            ]);
        }

        return response()->json(['status' => true, 'data' => $rows]);
    }

    /**
     * ──────────────────────────────────────────────────────────
     * GENERATE NO TRANSAKSI PENERIMAAN
     * Format: TRM + YYMMDD + urutan 3 digit
     * ──────────────────────────────────────────────────────────
     */
    protected function generateNoTransaksi(): string
    {
        $prefix = 'TRM' . date('ymd');

        $last = DB::table($this->tablePenerimaan)
            ->where('no_transaksi', 'like', $prefix . '%')
            ->orderByDesc('no_transaksi')
            ->value('no_transaksi');

        $lastNumber = $last ? (int) substr($last, -3) : 0;

        return $prefix . str_pad((string) ($lastNumber + 1), 3, '0', STR_PAD_LEFT);
    }
}