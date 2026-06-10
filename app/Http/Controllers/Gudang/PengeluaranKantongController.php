<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\IoResourceController;
use App\Services\PermintaanKantongService;
use App\Services\PengeluaranKantongService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class PengeluaranKantongController extends IoResourceController
{
    protected PermintaanKantongService  $permintaanService;
    protected PengeluaranKantongService $pengeluaranService;

    public function __construct(
        PermintaanKantongService  $permintaanService,
        PengeluaranKantongService $pengeluaranService
    ) {
        $this->permintaanService  = $permintaanService;
        $this->pengeluaranService = $pengeluaranService;
        $this->service            = $pengeluaranService;
        $this->viewPrefix         = 'app.gudang.pengeluaran_kantong';
        $this->itemVariable       = 'pengeluaran_kantong';
    }

    // ──────────────────────────────────────────────────────────
    // INDEX
    // ──────────────────────────────────────────────────────────
    public function index()
    {
        $hasStatus = Schema::hasColumn('permintaan_kantong', 'status');

        $query = DB::table('permintaan_kantong')->orderByDesc('id');

        if ($hasStatus) {
            $query->whereNotIn('status', ['SELESAI']);
        }

        // map agar $pm->status selalu ada di blade
        $permintaanList = $query->get()->map(function ($row) {
            $row->status = $row->status ?? 'PENDING';
            return $row;
        });

        return view("{$this->viewPrefix}.index", [
            'permintaanList' => $permintaanList,
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // GET DETAIL PERMINTAAN
    // tabel asli: permintaan_kantong_detail (tanpa s)
    // ──────────────────────────────────────────────────────────
    public function getPermintaan($id)
    {
        $hasJumlahDilayani = Schema::hasColumn('permintaan_kantong_detail', 'jumlah_dilayani');

        $details = DB::table('permintaan_kantong_detail as d')
            ->join('permintaan_kantong as h', 'h.id', '=', 'd.permintaan_kantong_id')
            ->where('d.permintaan_kantong_id', $id)
            ->select(
                'd.id',
                'h.nomor as kode',
                'd.merk',
                'd.jenis',
                'd.ukuran',
                'd.jumlah',
                'd.status',
                'd.created_at',
                DB::raw($hasJumlahDilayani
                    ? 'COALESCE(d.jumlah_dilayani, 0) as jumlah_dilayani'
                    : '0 as jumlah_dilayani'
                )
            )
            ->get()
            ->map(function ($row) {
                $row->status           = $row->status           ?? 'PENDING';
                $row->jumlah_dilayani  = $row->jumlah_dilayani  ?? 0;
                return $row;
            });

        return response()->json($details);
    }

    // ──────────────────────────────────────────────────────────
    // FIND — verifikasi no kantong saat scan
    // ──────────────────────────────────────────────────────────
    public function find(Request $request)
    {
        $no = preg_replace('/[^0-9]/', '', trim($request->no_kantong));

        if (!$no) {
            return response()->json(['status' => 'error', 'message' => 'No kantong tidak boleh kosong'], 422);
        }

        $data = DB::table('stok_kantong_masuk')
            ->whereRaw('RIGHT(no_kantong, ?) = ?', [strlen($no), $no])
            ->first();

        if (!$data) {
            return response()->json(['status' => 'notfound', 'message' => 'No kantong tidak ditemukan'], 404);
        }

        if (strtoupper($data->status ?? '') === 'KELUAR') {
            return response()->json(['status' => 'used', 'message' => 'Kantong sudah pernah dikeluarkan'], 400);
        }

        return response()->json([
            'status' => 'ok',
            'data'   => [
                'id'             => $data->id,
                'no_kantong'     => $data->no_kantong,
                'no_lot'         => $data->no_lot         ?? '',
                'merk'           => $data->merk           ?? $data->nama_merk ?? '—',
                'jenis'          => $data->jenis          ?? $data->jenis_darah ?? '—',
                'tipe'           => $data->tipe           ?? $data->golongan   ?? '—',
                'ukuran'         => $data->ukuran         ?? $data->volume     ?? '—',
                'tgl_kadaluarsa' => $data->tgl_kadaluarsa ?? $data->expired_at ?? null,
                'status'         => $data->status         ?? 'TERSEDIA',
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // SAVE
    // ──────────────────────────────────────────────────────────
    public function save(Request $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->all();

            if (empty($data['no_kantong']) || !is_array($data['no_kantong'])) {
                throw new \Exception('No kantong wajib berupa array');
            }
            if (empty($data['no_minta'])) {
                throw new \Exception('No permintaan wajib dipilih');
            }
            if (empty($data['detail_id'])) {
                throw new \Exception('Detail permintaan wajib dipilih');
            }

            // Ambil header permintaan
            $permintaan = DB::table('permintaan_kantong')
                ->where('id', $data['no_minta'])
                ->lockForUpdate()
                ->first();

            if (!$permintaan) throw new \Exception('Permintaan tidak ditemukan');

            // Ambil baris detail — tabel: permintaan_kantong_detail
            $detail = DB::table('permintaan_kantong_detail')
                ->where('id', $data['detail_id'])
                ->where('permintaan_kantong_id', $data['no_minta'])
                ->lockForUpdate()
                ->first();

            if (!$detail) throw new \Exception('Detail permintaan tidak ditemukan');

            $sudahDilayani = $detail->jumlah_dilayani ?? 0;
            $jumlahMinta   = $detail->jumlah          ?? 0;
            $totalScan     = count($data['no_kantong']);

            if (($sudahDilayani + $totalScan) > $jumlahMinta) {
                throw new \Exception('Jumlah melebihi permintaan. Sisa: ' . ($jumlahMinta - $sudahDilayani));
            }

            // MODE EDIT — rollback kantong lama
            if (!empty($data['id'])) {
                $old = DB::table('stok_kantong_keluar')->where('id', $data['id'])->first();
                if (!$old) throw new \Exception('Data edit tidak ditemukan');

                DB::table('stok_kantong_masuk')
                    ->where('no_kantong', $old->no_kantong)
                    ->update(['status' => 'TERSEDIA', 'updated_at' => now()]);

                if (!empty($old->detail_id)) {
                    DB::table('permintaan_kantong_detail')
                        ->where('id', $old->detail_id)
                        ->decrement('jumlah_dilayani', 1);
                }

                DB::table('stok_kantong_keluar')->where('id', $data['id'])->delete();
            }

            // INSERT setiap kantong
            foreach ($data['no_kantong'] as $kantong) {

                $stok = DB::table('stok_kantong_masuk')
                    ->where('no_kantong', $kantong)
                    ->lockForUpdate()
                    ->first();

                if (!$stok) throw new \Exception("Kantong $kantong tidak ditemukan di stok");
                if (strtoupper($stok->status) === 'KELUAR') throw new \Exception("Kantong $kantong sudah digunakan");
                if (DB::table('stok_kantong_keluar')->where('no_kantong', $kantong)->exists()) {
                    throw new \Exception("Kantong $kantong sudah pernah disimpan");
                }

                DB::table('stok_kantong_keluar')->insert([
                    'no_keluar'             => $data['no_keluar'],
                    'tgl_keluar'            => $data['tgl_keluar'],
                    'no_kantong'            => $kantong,
                    'no_lot'                => $stok->no_lot ?? null,
                    'merk'                  => $stok->merk   ?? null,
                    'jenis'                 => $stok->jenis  ?? null,
                    'tipe'                  => $stok->tipe   ?? null,
                    'ukuran'                => $stok->ukuran ?? null,
                    'tujuan'                => $data['tujuan']     ?? 'Pengeluaran',
                    'keterangan'            => $data['keterangan'] ?? null,
                    'detail_id'             => $detail->id,
                    'permintaan_kantong_id' => $permintaan->id,
                    'created_by'            => auth()->id() ?? 1,
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ]);

                DB::table('stok_kantong_masuk')
                    ->where('id', $stok->id)
                    ->update(['status' => 'KELUAR', 'updated_at' => now()]);
            }

            // Update jumlah_dilayani & status DETAIL
            $terpenuhi    = $sudahDilayani + $totalScan;
            $statusDetail = $terpenuhi >= $jumlahMinta ? 'SELESAI' : 'PROSES';

            DB::table('permintaan_kantong_detail')
                ->where('id', $detail->id)
                ->update([
                    'jumlah_dilayani' => $terpenuhi,
                    'status'          => $statusDetail,
                    'updated_at'      => now(),
                ]);

            // Sinkron status header
            $this->syncHeaderStatus($permintaan->id);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Pengeluaran berhasil disimpan']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    // ──────────────────────────────────────────────────────────
    // SYNC STATUS HEADER
    // ──────────────────────────────────────────────────────────
    protected function syncHeaderStatus(int $permintaanId): void
    {
        if (!Schema::hasColumn('permintaan_kantong', 'status')) return;

        $details = DB::table('permintaan_kantong_detail')
            ->where('permintaan_kantong_id', $permintaanId)
            ->get();

        if ($details->isEmpty()) return;

        $allSelesai = $details->every(fn($d) => ($d->status ?? '') === 'SELESAI');
        $anyProses  = $details->contains(fn($d) => in_array($d->status ?? '', ['PROSES', 'SELESAI']));

        $status = $allSelesai ? 'SELESAI' : ($anyProses ? 'PROSES' : 'PENDING');

        DB::table('permintaan_kantong')
            ->where('id', $permintaanId)
            ->update(['status' => $status, 'updated_at' => now()]);
    }

    // ──────────────────────────────────────────────────────────
    // LIST
    // ──────────────────────────────────────────────────────────
    public function list()
    {
        return response()->json(
            DB::table('stok_kantong_keluar')->orderByDesc('id')->get()
        );
    }

    // ──────────────────────────────────────────────────────────
    // SHOW
    // ──────────────────────────────────────────────────────────
    public function show($id)
    {
        $data = DB::table('stok_kantong_keluar')->where('id', $id)->first();

        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($data);
    }

    // ──────────────────────────────────────────────────────────
    // DELETE
    // ──────────────────────────────────────────────────────────
    public function delete($id)
    {
        DB::beginTransaction();

        try {
            $data = DB::table('stok_kantong_keluar')->where('id', $id)->first();

            if (!$data) throw new \Exception('Data tidak ditemukan');

            // Rollback stok
            DB::table('stok_kantong_masuk')
                ->where('no_kantong', $data->no_kantong)
                ->update(['status' => 'TERSEDIA', 'updated_at' => now()]);

            // Kurangi dilayani pada detail
            if (!empty($data->detail_id)) {
                $detail = DB::table('permintaan_kantong_detail')
                    ->where('id', $data->detail_id)
                    ->first();

                if ($detail) {
                    $baru   = max(0, ($detail->jumlah_dilayani ?? 1) - 1);
                    $status = $baru <= 0 ? 'PENDING'
                            : ($baru >= $detail->jumlah ? 'SELESAI' : 'PROSES');

                    DB::table('permintaan_kantong_detail')
                        ->where('id', $data->detail_id)
                        ->update([
                            'jumlah_dilayani' => $baru,
                            'status'          => $status,
                            'updated_at'      => now(),
                        ]);

                    if (!empty($data->permintaan_kantong_id)) {
                        $this->syncHeaderStatus($data->permintaan_kantong_id);
                    }
                }
            }

            DB::table('stok_kantong_keluar')->where('id', $id)->delete();

            DB::commit();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}