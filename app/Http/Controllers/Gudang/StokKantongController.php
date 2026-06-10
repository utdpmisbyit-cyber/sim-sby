<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\IoResourceController;
use App\Services\StokKantongService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokKantongController extends IoResourceController
{
    protected $viewPrefix   = 'app.gudang.stok';
    protected $itemVariable = 'stok';

    // JANGAN deklarasi typed property "$service" di sini —
    // IoResourceController sudah punya $service (untyped).
    // Cukup assign di constructor.

    public function __construct()
    {
        $this->service = new StokKantongService();
    }

    public function index()
    {
        return view("$this->viewPrefix.index");
    }

    // FIND — lookup by barcode/scan
    public function find(Request $r)
    {
        try {
            $data = DB::table('pendataan_kantong')
                ->where('barcode', $r->kode)
                ->first();

            if (!$data) {
                return response()->json(['status' => 'notfound', 'message' => 'No kantong tidak ditemukan']);
            }

            return response()->json([
                'status' => 'found',
                'data'   => [
                    'merk'   => $data->merk_kantong  ?? '',
                    'jenis'  => $data->jenis_kantong ?? '',
                    'tipe'   => $data->type_kantong  ?? $data->tipe ?? '',
                    'ukuran' => $data->ukuran        ?? '',
                    'no_lot' => $data->no_lot        ?? '',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // FIND KELUAR — cek kantong berstatus 'keluar' (untuk pengembalian)
    public function findKeluar(Request $r)
    {
        try {
            $data = DB::table('stok_kantong_masuk')
                ->whereNull('deleted_at')
                ->where('no_kantong', $r->kode)
                ->where('status', 'keluar')
                ->first();

            if (!$data) {
                $ada = DB::table('stok_kantong_masuk')
                    ->whereNull('deleted_at')
                    ->where('no_kantong', $r->kode)
                    ->first();

                if ($ada) {
                    return response()->json([
                        'status'  => 'invalid',
                        'message' => "Kantong ditemukan tapi status saat ini: {$ada->status}",
                    ]);
                }

                return response()->json(['status' => 'notfound', 'message' => 'No kantong tidak ditemukan di stok']);
            }

            return response()->json([
                'status' => 'found',
                'data'   => [
                    'no_kantong' => $data->no_kantong,
                    'merk'       => $data->merk   ?? '',
                    'jenis'      => $data->jenis  ?? '',
                    'tipe'       => $data->tipe   ?? '',
                    'ukuran'     => $data->ukuran ?? '',
                    'no_lot'     => $data->no_lot ?? '',
                    'status'     => $data->status,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // SAVE — simpan batch scan masuk
    public function save(Request $r)
    {
        $r->validate([
            'no_terima'      => 'required|string',
            'tgl_terima'     => 'required|date',
            'items'          => 'required|array|min:1',
            'items.*.kode'   => 'required|string',
            'items.*.merk'   => 'nullable|string',
            'items.*.jenis'  => 'nullable|string',
            'items.*.tipe'   => 'nullable|string',
            'items.*.ukuran' => 'nullable|string',
            'items.*.no_lot' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $now  = now();
            $rows = [];

            foreach ($r->items as $item) {
                $rows[] = [
                    'no_terima'  => $r->no_terima,
                    'tgl_terima' => $r->tgl_terima,
                    'no_kantong' => $item['kode'],
                    'merk'       => $item['merk']   ?? null,
                    'jenis'      => $item['jenis']  ?? null,
                    'tipe'       => $item['tipe']   ?? null,
                    'ukuran'     => $item['ukuran'] ?? null,
                    'no_lot'     => $item['no_lot'] ?? null,
                    'status'     => 'tersedia',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('stok_kantong_masuk')->insert($rows);
            DB::commit();

            return response()->json([
                'status'  => 'ok',
                'message' => count($rows) . ' data berhasil disimpan',
                'count'   => count($rows),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // SAVE KEMBALI — proses pengembalian kantong
    public function saveKembali(Request $r)
    {
        $r->validate([
            'no_kembali'         => 'required|string',
            'tgl_kembali'        => 'required|date',
            'keterangan'         => 'nullable|string|max:255',
            'items'              => 'required|array|min:1',
            'items.*.no_kantong' => 'required|string',
            'items.*.kondisi'    => 'required|in:baik,rusak',
        ]);

        try {
            $result = $this->service->prosesKembali(
                $r->items,
                $r->no_kembali,
                $r->tgl_kembali,
                $r->keterangan
            );

            return response()->json([
                'status'   => 'ok',
                'message'  => "{$result['berhasil']} kantong berhasil dikembalikan",
                'berhasil' => $result['berhasil'],
                'gagal'    => $result['gagal'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // LIST — masuk / keluar / kembali
    public function list(Request $r)
    {
        try {
            $tipe = $r->get('tipe', 'masuk');

            if ($tipe === 'keluar') {
                $data = DB::table('stok_kantong_masuk')
                    ->whereNull('deleted_at')
                    ->where('status', 'keluar')
                    ->orderByDesc('updated_at')
                    ->orderByDesc('id')
                    ->limit(500)
                    ->get();

            } elseif ($tipe === 'kembali') {
                $data = DB::table('pengembalian_kantong')
                    ->whereNull('deleted_at')
                    ->orderByDesc('tgl_kembali')
                    ->orderByDesc('id')
                    ->limit(500)
                    ->get();

            } else {
                $data = DB::table('stok_kantong_masuk')
                    ->whereNull('deleted_at')
                    ->orderByDesc('tgl_terima')
                    ->orderByDesc('id')
                    ->limit(500)
                    ->get();
            }

            return response()->json(['status' => 'ok', 'data' => $data]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'data' => []], 500);
        }
    }

    // SUMMARY — angka untuk kartu dashboard
    public function summary()
    {
        try {
            return response()->json([
                'status' => 'ok',
                'data'   => $this->service->getSummary(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}