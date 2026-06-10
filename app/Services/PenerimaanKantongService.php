<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\PenerimaanKantong;
use App\Models\PenerimaanKantongDetail;

class PenerimaanKantongService
{
    public function generateNo(): string
    {
        $prefix = date('ymd');
        $urut   = PenerimaanKantong::where('no_transaksi', 'like', $prefix . '%')->count() + 1;
        return $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Scan kantong — join ke permintaan_kantong_detail untuk ambil kode (no_permintaan).
     */
    public function getKantongByScan(string $no_kantong): object
    {
        $data = DB::table('stok_kantong_keluar as sk')
            ->leftJoin('permintaan_kantong_detail as pkd', 'pkd.id', '=', 'sk.detail_id')
            ->leftJoin('permintaan_kantong as pk', 'pk.id', '=', 'sk.permintaan_kantong_id')
            ->select([
                'sk.no_kantong',
                'sk.no_keluar',
                'sk.no_lot',
                'sk.merk',
                'sk.jenis',
                'sk.ukuran',
                'sk.tgl_keluar',
                'pkd.kode as no_permintaan',
                'pk.nomor as nomor_permintaan',
            ])
            ->where('sk.no_kantong', $no_kantong)
            ->whereNull('sk.deleted_at')
            ->first();

        if (! $data) {
            throw new \Exception("Nomor kantong [{$no_kantong}] tidak ditemukan di data keluar gudang.");
        }

        return $data;
    }

    /**
     * Jumlah kantong berdasarkan no_keluar.
     */
    public function getJumlahKirim(string $no_keluar): int
    {
        return DB::table('stok_kantong_keluar')
            ->where('no_keluar', $no_keluar)
            ->whereNull('deleted_at')
            ->count();
    }

    /**
     * Autocomplete No Gudang Keluar — cari di stok_kantong_keluar.no_keluar
     */
    public function searchNoKeluar(string $keyword): array
    {
        return DB::table('stok_kantong_keluar')
            ->whereNull('deleted_at')
            ->where('no_keluar', 'like', "%{$keyword}%")
            ->select('no_keluar', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('no_keluar')
            ->orderByDesc('jumlah')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Autocomplete No Permintaan — cari di permintaan_kantong_detail.kode
     * yang sudah ada di stok_kantong_keluar (sudah dikeluarkan gudang).
     */
    public function searchNoPermintaan(string $keyword): array
    {
        return DB::table('permintaan_kantong_detail as pkd')
            ->join('stok_kantong_keluar as sk', 'sk.detail_id', '=', 'pkd.id')
            ->whereNull('sk.deleted_at')
            ->whereNull('pkd.deleted_at')
            ->where('pkd.kode', 'like', "%{$keyword}%")
            ->select(
                'pkd.kode as no_permintaan',
                'sk.no_keluar',
                DB::raw('COUNT(sk.id) as jumlah_kantong')
            )
            ->groupBy('pkd.kode', 'sk.no_keluar')
            ->orderByDesc('jumlah_kantong')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Ambil semua kantong berdasarkan no_keluar (untuk auto-fill tabel scan).
     */
    public function getKantongByNoKeluar(string $no_keluar): array
    {
        return DB::table('stok_kantong_keluar as sk')
            ->leftJoin('permintaan_kantong_detail as pkd', 'pkd.id', '=', 'sk.detail_id')
            ->whereNull('sk.deleted_at')
            ->where('sk.no_keluar', $no_keluar)
            ->select([
                'sk.no_kantong',
                'sk.no_keluar',
                'sk.no_lot',
                'sk.merk',
                'sk.jenis',
                'sk.ukuran',
                'pkd.kode as no_permintaan',
            ])
            ->get()
            ->toArray();
    }

    /**
     * Ambil semua kantong berdasarkan kode permintaan (no_permintaan).
     */
    public function getKantongByNoPermintaan(string $no_permintaan): array
    {
        return DB::table('stok_kantong_keluar as sk')
            ->join('permintaan_kantong_detail as pkd', 'pkd.id', '=', 'sk.detail_id')
            ->whereNull('sk.deleted_at')
            ->whereNull('pkd.deleted_at')
            ->where('pkd.kode', $no_permintaan)
            ->select([
                'sk.no_kantong',
                'sk.no_keluar',
                'sk.no_lot',
                'sk.merk',
                'sk.jenis',
                'sk.ukuran',
                'pkd.kode as no_permintaan',
            ])
            ->get()
            ->toArray();
    }

    public function simpan(array $data): void
    {
        DB::beginTransaction();
        try {
            $penerimaan = PenerimaanKantong::create([
                'no_transaksi'    => $data['no_transaksi'],
                'tanggal'         => $data['tanggal'],
                'kode_permintaan' => $data['kode']      ?? null,
                'no_keluar'       => $data['no_keluar'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                if (PenerimaanKantongDetail::where('no_kantong', $item['no_kantong'])->exists()) {
                    throw new \Exception("Kantong [{$item['no_kantong']}] sudah pernah diterima sebelumnya.");
                }
                PenerimaanKantongDetail::create([
                    'penerimaan_id' => $penerimaan->id,
                    'no_kantong'    => $item['no_kantong'],
                    'merk'          => $item['merk']   ?? null,
                    'jenis'         => $item['jenis']  ?? null,
                    'ukuran'        => $item['ukuran'] ?? null,
                    'no_lot'        => $item['no_lot'] ?? null,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getStokSummary(int $penerimaanId): array
    {
        $noKantongList = PenerimaanKantongDetail::where('penerimaan_id', $penerimaanId)
            ->pluck('no_kantong')->toArray();

        $totalTerima = count($noKantongList);
        if ($totalTerima === 0) {
            return ['total_terima' => 0, 'sudah_sample' => 0, 'sudah_serologi' => 0, 'sisa_stok' => 0];
        }

        $sudahSample   = DB::table('pengiriman_sample_detail')->whereIn('no_kantong', $noKantongList)->count();
        $sudahSerologi = DB::table('pengiriman_serologi_detail')->whereIn('no_kantong', $noKantongList)->count();

        return [
            'total_terima'   => $totalTerima,
            'sudah_sample'   => $sudahSample,
            'sudah_serologi' => $sudahSerologi,
            'sisa_stok'      => max(0, $totalTerima - $sudahSample - $sudahSerologi),
        ];
    }

    public function getDetailWithStatus(int $penerimaanId): \Illuminate\Support\Collection
    {
        return PenerimaanKantongDetail::where('penerimaan_id', $penerimaanId)
            ->get()
            ->map(function ($item) {
                $inSample = DB::table('pengiriman_sample_detail as psd')
                    ->join('pengiriman_sample as ps', 'ps.id', '=', 'psd.pengiriman_sample_id')
                    ->where('psd.no_kantong', $item->no_kantong)
                    ->select('ps.no_fpd')->first();

                $inSerologi = DB::table('pengiriman_serologi_detail as srd')
                    ->join('pengiriman_serologi as sr', 'sr.id', '=', 'srd.pengiriman_serologi_id')
                    ->where('srd.no_kantong', $item->no_kantong)
                    ->select('sr.kode')->first();

                if ($inSerologi) {
                    $item->status_kirim = 'serologi';
                    $item->info_kirim   = $inSerologi->kode;
                } elseif ($inSample) {
                    $item->status_kirim = 'sample';
                    $item->info_kirim   = $inSample->no_fpd;
                } else {
                    $item->status_kirim = 'tersedia';
                    $item->info_kirim   = null;
                }

                return $item;
            });
    }
}