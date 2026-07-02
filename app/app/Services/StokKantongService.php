<?php

namespace App\Services;

use App\Models\StokKantong;
use Illuminate\Support\Facades\DB;

class StokKantongService extends IoService
{
    public function __construct()
    {
        $this->model   = new StokKantong();
        $this->sort_by = ['tgl_terima' => 'desc', 'id' => 'desc'];
        $this->filters = ['no_kantong', 'status', 'merk', 'jenis'];
    }

    // ─────────────────────────────────────────────────────────
    // DYNAMIC SEARCH — dipanggil oleh IoService::index()
    // ─────────────────────────────────────────────────────────
    public function dynamic_search($model, $params = [])
    {
        $no_kantong = $params['no_kantong'] ?? '';
        $status     = $params['status']     ?? '';
        $merk       = $params['merk']       ?? '';
        $jenis      = $params['jenis']      ?? '';

        if ($no_kantong !== '') {
            $model = $model->where('no_kantong', 'like', '%' . $no_kantong . '%');
        }
        if ($status !== '') {
            $model = $model->where('status', $status);
        }
        if ($merk !== '') {
            $model = $model->where('merk', 'like', '%' . $merk . '%');
        }
        if ($jenis !== '') {
            $model = $model->where('jenis', 'like', '%' . $jenis . '%');
        }

        return $model;
    }

    // ─────────────────────────────────────────────────────────
    // SUMMARY — jumlah stok masuk / keluar / tersedia / kembali
    // ─────────────────────────────────────────────────────────
    public function getSummary(): array
    {
        $masuk    = DB::table('stok_kantong_masuk')->whereNull('deleted_at')->count();
        $keluar   = DB::table('stok_kantong_masuk')->whereNull('deleted_at')->where('status', 'keluar')->count();
        $kembali  = DB::table('pengembalian_kantong')->whereNull('deleted_at')->count();
        $tersedia = DB::table('stok_kantong_masuk')->whereNull('deleted_at')->where('status', 'tersedia')->count();
        $rusak    = DB::table('stok_kantong_masuk')->whereNull('deleted_at')->where('status', 'rusak')->count();

        return [
            'masuk'    => $masuk,
            'keluar'   => $keluar,
            'kembali'  => $kembali,
            'tersedia' => $tersedia,
            'rusak'    => $rusak,
        ];
    }

    // ─────────────────────────────────────────────────────────
    // PROSES PENGEMBALIAN — update status stok jadi tersedia
    // ─────────────────────────────────────────────────────────
    public function prosesKembali(array $items, string $noKembali, string $tglKembali, ?string $keterangan = null): array
    {
        $berhasil = 0;
        $gagal    = [];

        DB::beginTransaction();
        try {
            $now = now();

            foreach ($items as $item) {
                $noKantong = $item['no_kantong'];
                $kondisi   = $item['kondisi'] ?? 'baik'; // baik | rusak

                // Cari di stok_kantong_masuk yang statusnya 'keluar'
                $stok = DB::table('stok_kantong_masuk')
                    ->whereNull('deleted_at')
                    ->where('no_kantong', $noKantong)
                    ->where('status', 'keluar')
                    ->first();

                if (!$stok) {
                    $gagal[] = [
                        'no_kantong' => $noKantong,
                        'alasan'     => 'Tidak ditemukan atau status bukan keluar',
                    ];
                    continue;
                }

                // Status baru berdasarkan kondisi saat kembali
                $statusBaru = ($kondisi === 'rusak') ? 'rusak' : 'tersedia';

                // Update status di stok_kantong_masuk
                DB::table('stok_kantong_masuk')
                    ->where('id', $stok->id)
                    ->update([
                        'status'     => $statusBaru,
                        'updated_at' => $now,
                    ]);

                // Catat ke tabel pengembalian_kantong
                DB::table('pengembalian_kantong')->insert([
                    'no_kembali'       => $noKembali,
                    'tgl_kembali'      => $tglKembali,
                    'no_kantong'       => $noKantong,
                    'stok_kantong_id'  => $stok->id,
                    'merk'             => $stok->merk,
                    'jenis'            => $stok->jenis,
                    'tipe'             => $stok->tipe,
                    'ukuran'           => $stok->ukuran,
                    'kondisi'          => $kondisi,
                    'keterangan'       => $keterangan,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);

                $berhasil++;
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'berhasil' => $berhasil,
            'gagal'    => $gagal,
        ];
    }
}