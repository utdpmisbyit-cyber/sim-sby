<?php

namespace App\Services;

use App\Models\CrossTest;
use App\Models\PermintaanFpup;
use App\Models\StokDarah;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CrossTestService
{
    public function findByNoFpup(string $noFpup): ?array
    {
        $fpup = DB::table('permintaan_fpup')
            ->where('no_fpup', $noFpup)
            ->first();

        if (!$fpup) {
            return null;
        }

        // Detail permintaan darah
        $details = DB::table('permintaan_fpup_detail')
            ->where('permintaan_fpup_id', $fpup->id)
            ->get();

        // Tambahkan relasi detail ke object fpup
        $fpup->details = $details;

        return [
            'fpup' => $fpup,
            'cross_tests' => CrossTest::where('permintaan_fpup_id', $fpup->id)
                ->orderByDesc('id')
                ->get(),
        ];
    }

    /**
     * Cari data kantong dari tabel stok_darah berdasarkan no_stok.
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
            'status_stok'    => $stok->status_stok,
        ];
    }

      public function saveCrossTest(array $data): CrossTest
    {
        return DB::transaction(function () use ($data) {

            $fpup = DB::table('permintaan_fpup')
                ->where('id', $data['permintaan_fpup_id'])
                ->first();

            $detail = DB::table('permintaan_fpup_detail')
                ->where('permintaan_fpup_id', $data['permintaan_fpup_id'])
                ->first();

            return CrossTest::updateOrCreate(
                [
                    'permintaan_fpup_id' => $data['permintaan_fpup_id'],
                    'no_stock'           => $data['no_stock'],
                ],
                [
                    'no_fpup'      => $data['no_fpup'],

                    'nama_pasien'  => $fpup->nama_pasien ?? null,

                    'gol'          => $detail->gol_darah ?? null,
                    'rhesus'       => $detail->rhesus ?? null,

                    'jns_darah'    => $data['jns_darah'] ?? null,
                    'gol_rh_kantong' => $data['gol_rh_kantong'] ?? null,

                    'tgl_ambil'      => $data['tgl_ambil'] ?? null,
                    'tgl_produksi'   => $data['tgl_produksi'] ?? null,
                    'tgl_kadaluarsa' => $data['tgl_kadaluarsa'] ?? null,

                    'referal'      => !empty($fpup->pasien_referal) ? 'YA' : 'TIDAK',
                    'no_referal'   => $fpup->no_reg_online ?? null,
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

    public function delete(CrossTest $crossTest): void
    {
        $crossTest->delete();
    }
}