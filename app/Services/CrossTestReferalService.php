<?php

namespace App\Services;

use App\Models\CrossTestReferal;
use App\Models\StokDarah;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CrossTestReferalService
{
    /**
     * Cari header FPUP Referal + detail (join jenis_darah) + cross test yang sudah ada.
     */
    public function findByNoFpup(string $noFpup): ?array
    {
        $fpup = DB::table('permintaan_fpup_referal')
            ->where('no_fpup', $noFpup)
            ->first();

        if (!$fpup) {
            return null;
        }

        // Detail permintaan darah, join ke jenis_darah (jns_darah = FK id)
        $details = DB::table('permintaan_fpup_referal_detail as d')
            ->leftJoin('jenis_darah as jd', 'jd.id', '=', 'd.jns_darah')
            ->where('d.permintaan_fpup_referal_id', $fpup->id)
            ->select(
                'd.*',
                'jd.id as jenis_darah_id',
                'jd.kode as jenis_darah_kode',
                'jd.nama as jenis_darah_nama',
                'jd.nama_pendek as jenis_darah_nama_pendek',
                'jd.umur_darah as jenis_darah_umur'
            )
            ->get();

        // Tambahkan relasi detail ke object fpup (dipakai di view/JS)
        $fpup->details = $details;

        return [
            'fpup' => $fpup,
            'cross_tests' => CrossTestReferal::where('permintaan_fpup_referal_id', $fpup->id)
                ->orderByDesc('id')
                ->get(),
        ];
    }

    /**
     * Cari data kantong dari tabel stok_darah berdasarkan no_stok.
     *
     * PERBAIKAN: ditambahkan field 'tgl_aftap' karena front-end (JS simpanKantong())
     * membaca stok.tgl_aftap saat menyimpan cross match. Sebelumnya field ini tidak
     * dikembalikan sama sekali sehingga selalu fallback ke tanggal hari ini.
     * Sesuaikan nama kolom 'tgl_aftap' di bawah ini dengan nama kolom asli pada
     * tabel stok_darah Anda (cek dengan: php artisan db:table stok_darah).
     */
    public function findStokByNoStock(string $noStock): ?array
    {
        $stok = StokDarah::where('no_stok', $noStock)->first();
        if (!$stok) return null;

        return [
            'no_stock'       => $stok->no_stok,
            'jns_darah'      => $stok->jenis_darah,
            'gol_rh_kantong' => ($stok->golongan_darah ?? '') . ($stok->rhesus ?? ''),
            'tgl_produksi'   => $stok->tgl_produksi?->format('Y-m-d'),
            'tgl_kadaluarsa' => $stok->tgl_expired?->format('Y-m-d'),
            'tgl_aftap'      => $stok->tgl_aftap?->format('Y-m-d'), // sesuaikan nama kolom jika berbeda
            'status_stok'    => $stok->status_stok,
        ];
    }

    /**
     * Simpan / update cross test referal untuk satu no_stock.
     */
    public function saveCrossTest(array $data): CrossTestReferal
    {
        return DB::transaction(function () use ($data) {

            $fpup = DB::table('permintaan_fpup_referal')
                ->where('id', $data['permintaan_fpup_referal_id'])
                ->first();

            // Detail terkait — pakai detail spesifik jika dikirim, kalau tidak ambil yang pertama
            $detailQuery = DB::table('permintaan_fpup_referal_detail as d')
                ->leftJoin('jenis_darah as jd', 'jd.id', '=', 'd.jns_darah')
                ->where('d.permintaan_fpup_referal_id', $data['permintaan_fpup_referal_id'])
                ->select('d.*', 'jd.id as jenis_darah_id', 'jd.nama as jenis_darah_nama');

            if (!empty($data['permintaan_fpup_referal_detail_id'])) {
                $detailQuery->where('d.id', $data['permintaan_fpup_referal_detail_id']);
            }

            $detail = $detailQuery->first();

            return CrossTestReferal::updateOrCreate(
                [
                    'permintaan_fpup_referal_id' => $data['permintaan_fpup_referal_id'],
                    'no_stock'                   => $data['no_stock'],
                ],
                [
                    'permintaan_fpup_referal_detail_id' => $detail->id ?? null,
                    'no_fpup'    => $data['no_fpup'],
                    'no_referal' => $fpup->no_referal ?? null,

                    'nama_pasien' => $fpup->nama_pasien ?? null,
                    'gol'         => $detail->gol_darah ?? null,
                    'rhesus'      => $detail->rhesus ?? null,

                    'jenis_darah_id' => $detail->jenis_darah_id ?? null,
                    'jns_darah'      => $data['jns_darah'] ?? ($detail->jenis_darah_nama ?? null),
                    'gol_rh_kantong' => $data['gol_rh_kantong'] ?? null,

                    'tgl_ambil'      => $data['tgl_ambil'] ?? null,
                    'tgl_produksi'   => $data['tgl_produksi'] ?? null,
                    'tgl_kadaluarsa' => $data['tgl_kadaluarsa'] ?? null,

                    'referal'      => !empty($fpup->pasien_referal) ? 'YA' : 'TIDAK',
                    'kurir_online' => $fpup->no_reg_online ?? null,
                    'tgl_online'   => $fpup->tgl_registrasi_online ?? null,

                    'pemeriksa' => $data['pemeriksa']
                        ?? (Auth::user()?->name ?? 'Sistem'),

                    'tgl_periksa' => now(),
                    'status' => $this->resolveStatus($data),
                ]
            );
        });
    }

    private function resolveStatus(array $data): string
    {
        $mayor = $data['hasil_mayor'] ?? null;
        $minor = $data['hasil_minor'] ?? null;

        if ($mayor === 'Incompatible' || $minor === 'Incompatible') return 'incompatible';
        if ($mayor === 'Compatible' && $minor === 'Compatible') return 'compatible';
        return 'proses';
    }

    public function delete(CrossTestReferal $crossTest): void
    {
        $crossTest->delete();
    }
}