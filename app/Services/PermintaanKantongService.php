<?php

namespace App\Services;

use App\Models\PermintaanKantong;
use App\Models\PermintaanKantongDetail;
use App\Models\TipeKantong;
use Illuminate\Support\Facades\DB;

class PermintaanKantongService extends IoService
{
    public function __construct()
    {
        $this->model = new PermintaanKantong();
        $this->with  = ['details'];
        $this->sort_by = ['tanggal' => 'desc'];
    }

    // =========================
    // HELPER — hitung status header dari status semua detail
    // =========================
    protected function resolveStatus($details)
    {
        if ($details->isEmpty()) return 'PENDING';

        $allSelesai = $details->every(fn($d) => strtoupper($d->status ?? '') === 'SELESAI');
        if ($allSelesai) return 'SELESAI';

        $anyProses = $details->contains(
            fn($d) => in_array(strtoupper($d->status ?? ''), ['PROSES', 'SELESAI'])
        );
        if ($anyProses) return 'PROSES';

        return 'PENDING';
    }

    // =========================
    // LIST (HISTORY)
    // =========================
    public function list()
    {
        return PermintaanKantong::with('details')
            ->latest()
            ->get()
            ->map(function ($row) {
                return [
                    'id'     => $row->id,
                    'kode'   => $row->nomor,
                    'tanggal_minta' => $row->tanggal,
                    'status' => $this->resolveStatus($row->details),
                    'jumlah' => $row->details->sum('jumlah'),
                    'merk'   => optional($row->details->first())->merk,
                    'jenis'  => optional($row->details->first())->jenis,
                    'ukuran' => optional($row->details->first())->ukuran,
                ];
            });
    }

    // =========================
    // GENERATE NO (ANTI DUPLICATE)
    // =========================
    public function generateNo()
    {
        $prefix = 'PK'.date('ym'); // 2605

        $last = PermintaanKantong::where('nomor', 'like', $prefix . '%')
            ->orderBy('nomor', 'desc')
            ->first();

        if (!$last) {
            return $prefix . '0001';
        }

        $lastNumber = (int) substr($last->nomor, -4);

        return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }

    // =========================
    // STORE
    // =========================
    public function store($payload)
    {
        return DB::transaction(function () use ($payload) {

            $header = PermintaanKantong::create([
                'nomor' => $payload['kode'],
                'tanggal' => $payload['tanggal_minta'],
                'bagian_petugas_id' => 1,
                'petugas_id' => 1,
            ]);

            foreach ($payload['items'] as $item) {
                PermintaanKantongDetail::create([
                    'permintaan_kantong_id' => $header->id,
                    'kode'   => $payload['kode'],
                    'merk'   => $item['merk'],
                    'jenis'  => $item['jenis'],
                    'ukuran' => $item['ukuran'],
                    'jumlah' => $item['jumlah'],
                    'status' => 'PENDING',
                ]);
            }

            return $header;
        });
    }

    // =========================
    // FIND (EDIT)
    // =========================
    public function find($value, $column = 'id')
    {
        $row = PermintaanKantong::with('details')
            ->where($column, $value)
            ->first();

        if (!$row) return null;

        return [
            'id' => $row->id,
            'kode' => $row->nomor,
            'tanggal_minta' => $row->tanggal,
            'status' => $this->resolveStatus($row->details),
            'items' => $row->details->map(fn($d) => [
                'id'     => $d->id,
                'merk'   => $d->merk,
                'jenis'  => $d->jenis,
                'ukuran' => $d->ukuran,
                'jumlah' => $d->jumlah,
                'jumlah_dilayani' => $d->jumlah_dilayani ?? 0,
                'status' => $d->status ?? 'PENDING',
            ])->values()
        ];
    }

    public function show($id)
    {
        $data = $this->find($id);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // =========================
    // UPDATE
    // =========================
    public function update($id, $payload)
    {
        return DB::transaction(function () use ($id, $payload) {

            $header = PermintaanKantong::with('details')->find($id);

            if (!$header) return false;

            // ⚠️ Cegah edit kalau sudah ada proses pengeluaran (jumlah_dilayani > 0)
            // supaya progres yang sudah tercatat di stok_kantong_keluar tidak
            // "hilang" ketika detail lama dihapus & dibuat ulang.
            $sudahDiproses = $header->details->contains(fn($d) => ($d->jumlah_dilayani ?? 0) > 0);
            if ($sudahDiproses) {
                throw new \Exception('Permintaan ini sudah memiliki proses pengeluaran, tidak bisa diedit lagi.');
            }

            $tanggal = !empty($payload['tanggal_minta'])
                ? $payload['tanggal_minta']
                : now()->toDateString();

            $header->update([
                'tanggal' => $tanggal
            ]);

            // hapus detail lama
            PermintaanKantongDetail::where('permintaan_kantong_id', $id)->delete();

            foreach ($payload['items'] as $item) {
                PermintaanKantongDetail::create([
                    'permintaan_kantong_id' => $id,
                    'kode'   => $header->nomor,
                    'merk'   => $item['merk'],
                    'jenis'  => $item['jenis'],
                    'ukuran' => $item['ukuran'],
                    'jumlah' => $item['jumlah'],
                    'status' => 'PENDING',
                ]);
            }

            return true;
        });
    }

    // =========================
    // DELETE
    // =========================
    public function delete($id)
    {
        return DB::transaction(function () use ($id) {

            PermintaanKantongDetail::where('permintaan_kantong_id', $id)->delete();
            return PermintaanKantong::where('id', $id)->delete();
        });
    }
}