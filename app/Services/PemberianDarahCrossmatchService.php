<?php

namespace App\Services;

use App\Models\PemberianDarahCrossmatch;
use App\Models\PemberianDarahCrossmatchDetail;
use App\Models\PermintaanFpup;
use App\Models\PelayananCrosstest;
use App\Models\Petugas;
use App\Models\CrossTest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PemberianDarahCrossmatchService
{
    /* ------------------------------------------------------------------ */
    /*  Listing / Search                                                   */
    /* ------------------------------------------------------------------ */

    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = PemberianDarahCrossmatch::with('details')
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(function ($q) use ($s) {
                $q->where('no_pemberian', 'like', "%$s%")
                  ->orWhere('no_fpup',    'like', "%$s%")
                  ->orWhere('pasien',     'like', "%$s%")
                  ->orWhere('dokter',     'like', "%$s%")
                  ->orWhere('nama_rs',    'like', "%$s%");
            });
        }

        if (!empty($filters['tanggal_dari'])) {
            $query->whereDate('tanggal', '>=', $filters['tanggal_dari']);
        }

        if (!empty($filters['tanggal_sampai'])) {
            $query->whereDate('tanggal', '<=', $filters['tanggal_sampai']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate(15)->withQueryString();
    }

    
    public function create(array $data): PemberianDarahCrossmatch
    {
        return DB::transaction(function () use ($data) {
            $data['no_pemberian']       = PemberianDarahCrossmatch::generateNoPemberian();
            $data['jam_keluar']         = now()->format('H:i:s');
            $data['is_kandungan']       = (bool) ($data['is_kandungan'] ?? false);
            $data['is_pasien_referral'] = (bool) ($data['is_pasien_referral'] ?? false);
            
            $details = $data['details'] ?? [];
            unset($data['details']);

            $pemberian = PemberianDarahCrossmatch::create($data);
            DB::table('cross_tests')->where('no_fpup', $data['no_fpup'])
            ->update([
                'status' => 'selesai',
                'updated_at' => now(),
            ]);
            DB::table('pelayanan_crosstest')->where('no_fpup', $data['no_fpup'])
                ->update([
                    'status' => 'selesai',
                    'updated_at' => now(),
                ]);
            foreach ($details as $detail) {
                if (!empty($detail['nostock'])) {
                    $pemberian->details()->create($detail);
                }
            }

            return $pemberian->load('details');
        });
    }

    public function update(PemberianDarahCrossmatch $pemberian, array $data): PemberianDarahCrossmatch
    {
        return DB::transaction(function () use ($pemberian, $data) {
            $data['is_kandungan']       = (bool) ($data['is_kandungan'] ?? false);
            $data['is_pasien_referral'] = (bool) ($data['is_pasien_referral'] ?? false);

            $details = $data['details'] ?? [];
            unset($data['details']);

            $pemberian->update($data);

            DB::table('cross_tests')->where('no_fpup', $pemberian->no_fpup)
                ->update([
                    'status' => 'selesai'
                ]);

            DB::table('pelayanan_crosstest')->where('no_fpup', $pemberian->no_fpup)
                ->update([
                    'status' => 'selesai'
                ]);

            $pemberian->details()->delete();
            foreach ($details as $detail) {
                if (!empty($detail['nostock'])) {
                    $pemberian->details()->create($detail);
                }
            }

            return $pemberian->fresh('details');
        });
    }

    public function delete(PemberianDarahCrossmatch $pemberian): bool
    {
        return DB::transaction(function () use ($pemberian) {
            $pemberian->details()->delete();
            return (bool) $pemberian->delete();
        });
    }

  
    public function scanFpup(string $noFpup): ?array
    {
        $fpup = PermintaanFpup::with('details')
            ->where('no_fpup', $noFpup)
            ->first();

        if (!$fpup) {
            return null;
        }
         $transaksi = PemberianDarahCrossmatch::where('no_fpup', $noFpup)->first();

         $cross = CrossTest::where('no_fpup', $noFpup)->first();
         $detailPertama = $fpup->details->first();

        return [
            'no_fpup'          => $fpup->no_fpup,
            'tgl_fpup'         => $fpup->tgl_minta,
            'dokter'           => $fpup->nama_dokter,
            'kode_rs'          => $fpup->kode_rs,
            'nama_rs'          => $fpup->nama_rs,
            'pasien'           => $fpup->nama_pasien,
            'jenis_rs'         => $fpup->jenis_rs,
            'kelas_rawat'      => $fpup->kelas_rawat,
            'gol_darah_pasien' => $cross->gol ?? $detailPertama?->gol_darah ?? '',
            'rh_pasien'        => $cross->rhesus ?? $detailPertama?->rhesus ?? '',
            'utdd_lain'        => $cross?->referal ?? '',
            'kategori'         => $fpup->kategori_rs,
            'utdd_lain'        => $fpup->nama_os,
            'jns_biaya'        => $fpup->jns_biaya,

            'no_registrasi_online'  => $fpup->no_reg_online,
            'tgl_registrasi_online' => $fpup->tgl_registrasi_online,
              'warning'          => $transaksi ? true : false,
            'warning_message'  => $transaksi
                ? 'No FPUP sudah pernah digunakan pada transaksi pemberian darah.'
                : null,
            'jenis_darah' => $fpup->details->map(function ($d) use ($transaksi) {
                return [
                    'jns_darah' => $d->jns_darah,
                    'gol'       => $d->gol_darah,
                    'rh'        => $d->rhesus,
                    'cc'        => $d->cc,
                    'jumlah'    => $d->jumlah,
                    'dipenuhi'  => 0,
                    
                ];

            })->values()->toArray(),
        ];
    }

   
    public function scanStock(string $nostock): ?array
    {
        $stock = PelayananCrosstest::query()
            ->leftJoin(
                'permintaan_fpup_detail',
                'pelayanan_crosstest.permintaan_fpup_id',
                '=',
                'permintaan_fpup_detail.permintaan_fpup_id'
            )
            ->leftJoin(
                'cross_tests',
                'pelayanan_crosstest.no_stock',
                '=',
                'cross_tests.no_stock'
            )
            ->where('pelayanan_crosstest.no_stock', $nostock)
            ->select(
                'pelayanan_crosstest.no_stock',
                'pelayanan_crosstest.jns_darah',
                'pelayanan_crosstest.gol',
                'pelayanan_crosstest.rhesus',
                'permintaan_fpup_detail.cc',
                'cross_tests.tgl_kadaluarsa'
            )
            ->first();
           $alreadyUsed = PemberianDarahCrossmatchDetail::where(
                'nostock',
                $nostock
            )->exists();

            if ($alreadyUsed) {
                return [
                    'warning' => true,
                    'message' => 'No Stock sudah pernah digunakan pada transaksi pemberian darah.'
                ];
            }
        if (!$stock) {
            return null;
        }

        return [
            'nostock'     => $stock->no_stock,
            'jns_darah'   => $stock->jns_darah,
            'gol'         => $stock->gol,
            'rh'          => $stock->rhesus,
            'cc'          => $stock->cc,
            'tgl_expired' => $stock->tgl_kadaluarsa
                ? \Carbon\Carbon::parse($stock->tgl_kadaluarsa)->format('Y-m-d')
                : null,
            
        ];
    }

    /**
     * Scan kode petugas → kembalikan data petugas.
     *
     * TODO: Sesuaikan query dengan model/tabel user di sistem Anda.
     */
    public function scanPetugas(string $kode): ?array
    {
       $petugas = Petugas::where('kode', $kode)
        ->first();

        if (!$petugas) {
            return null;
        }

        return [
            'kode' => $petugas->kode,
            'nama' => $petugas->nama,
        ];
    }
}