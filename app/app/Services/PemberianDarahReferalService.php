<?php

namespace App\Services;

use App\Models\PemberianDarahReferal;
use App\Models\PemberianDarahReferalDetail;
use App\Models\PermintaanFpupReferal;
use App\Models\PelayananCrosstestReferal;
use App\Models\Petugas;
use App\Models\CrossTestReferal;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PemberianDarahReferalService
{
    /* ------------------------------------------------------------------ */
    /*  Listing / Search                                                   */
    /* ------------------------------------------------------------------ */

    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = PemberianDarahReferal::with('details')
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

    public function create(array $data): PemberianDarahReferal
    {
        return DB::transaction(function () use ($data) {
            $data['no_pemberian']    = PemberianDarahReferal::generateNoPemberian();
            $data['jam_keluar']      = now()->format('H:i:s');
            $data['is_kadaluarsa']   = (bool) ($data['is_kadaluarsa'] ?? false);
            $data['is_pasien_bayi']  = (bool) ($data['is_pasien_bayi'] ?? false);

            $details = $data['details'] ?? [];
            unset($data['details']);

            $pemberian = PemberianDarahReferal::create($data);

            // Tandai permintaan crossmatch referal terkait sebagai selesai
            // (nama tabel disamakan dengan modul referal: *_referal)
            DB::table('cross_tests_referal')->where('no_fpup', $data['no_fpup'])
                ->update([
                    'status'     => 'selesai',
                    'updated_at' => now(),
                ]);
            DB::table('pelayanan_crosstest_referal')->where('no_fpup', $data['no_fpup'])
                ->update([
                    'status'     => 'selesai',
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

    public function update(PemberianDarahReferal $pemberian, array $data): PemberianDarahReferal
    {
        return DB::transaction(function () use ($pemberian, $data) {
            $data['is_kadaluarsa']  = (bool) ($data['is_kadaluarsa'] ?? false);
            $data['is_pasien_bayi'] = (bool) ($data['is_pasien_bayi'] ?? false);

            $details = $data['details'] ?? [];
            unset($data['details']);

            $pemberian->update($data);

            DB::table('cross_tests_referal')->where('no_fpup', $pemberian->no_fpup)
                ->update([
                    'status'     => 'selesai',
                    'updated_at' => now(),
                ]);

            DB::table('pelayanan_crosstest_referal')->where('no_fpup', $pemberian->no_fpup)
                ->update([
                    'status'     => 'selesai',
                    'updated_at' => now(),
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

    public function delete(PemberianDarahReferal $pemberian): bool
    {
        return DB::transaction(function () use ($pemberian) {
            $pemberian->details()->delete();
            return (bool) $pemberian->delete();
        });
    }

    public function scanFpup(string $noFpup): ?array
    {
        // Sebelumnya memakai model PermintaanFpup & CrossTest yang tidak
        // pernah di-import (akan error Class not found). Disamakan dengan
        // use statement di atas: PermintaanFpupReferal & CrossTestReferal.
        $fpup = PermintaanFpupReferal::with('details')
            ->where('no_fpup', $noFpup)
            ->first();

        if (!$fpup) {
            return null;
        }

        $transaksi = PemberianDarahReferal::where('no_fpup', $noFpup)->first();

        $cross         = CrossTestReferal::where('no_fpup', $noFpup)->first();
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
            'kategori'         => $fpup->kategori_rs,
            // Catatan: sebelumnya key 'utdd_lain' ditulis dua kali (saling timpa
            // oleh $fpup->nama_os). Dipilih $cross?->referal karena namanya
            // lebih sesuai konsep "UTDD Lain" (UTD lain yang merujuk pasien).
            // Sesuaikan kembali jika 'nama_os' yang benar dipakai di data Anda.
            'utdd_lain'        => $cross?->referal ?? '',
            'jns_biaya'        => $fpup->jns_biaya,

            'no_registrasi_online'  => $fpup->no_reg_online,
            'tgl_registrasi_online' => $fpup->tgl_registrasi_online,

            'warning'         => $transaksi ? true : false,
            'warning_message' => $transaksi
                ? 'No FPUP sudah pernah digunakan pada transaksi pemberian darah.'
                : null,

            'jenis_darah' => $fpup->details->map(function ($d) {
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
        // Cek dulu apakah stok ini sudah pernah dipakai pada transaksi
        // pemberian darah lain, sebelum query detail stok.
        $alreadyUsed = PemberianDarahReferalDetail::where('nostock', $nostock)->exists();

        if ($alreadyUsed) {
            return [
                'warning' => true,
                'message' => 'No Stock sudah pernah digunakan pada transaksi pemberian darah.',
            ];
        }

        $stock = PelayananCrosstestReferal::query()
            ->leftJoin(
                'cross_tests_referal',
                'pelayanan_crosstest_referal.no_stock',
                '=',
                'cross_tests_referal.no_stock'
            )
            ->where('pelayanan_crosstest_referal.no_stock', $nostock)
            ->select(
                'pelayanan_crosstest_referal.no_stock',
                'pelayanan_crosstest_referal.jns_darah',
                'pelayanan_crosstest_referal.gol',
                'pelayanan_crosstest_referal.rhesus',
                'cross_tests_referal.tgl_kadaluarsa'
                // Catatan: kolom 'metode' & 'hasil' TIDAK ditarik otomatis dari
                // cross_tests_referal lagi karena kolom tersebut tidak ada di
                // tabel Anda (sempat menebak salah). Metode/Hasil sekarang
                // diisi manual oleh petugas per baris kantong di form (lihat
                // perubahan pada form.blade.php).
            )
            ->first();

        if (!$stock) {
            return null;
        }

        return [
            'nostock'     => $stock->no_stock,
            'jns_darah'   => $stock->jns_darah,
            'gol'         => $stock->gol,
            'rh'          => $stock->rhesus,
            'tgl_expired' => $stock->tgl_kadaluarsa
                ? \Carbon\Carbon::parse($stock->tgl_kadaluarsa)->format('Y-m-d')
                : null,
            'metode'      => null,
            'hasil'       => null,
            'keterangan'  => null,
        ];
    }

    /**
     * Scan kode petugas → kembalikan data petugas.
     *
     * TODO: Sesuaikan query dengan model/tabel user di sistem Anda.
     */
    public function scanPetugas(string $kode): ?array
    {
        $petugas = Petugas::where('kode', $kode)->first();

        if (!$petugas) {
            return null;
        }

        return [
            'kode' => $petugas->kode,
            'nama' => $petugas->nama,
        ];
    }
}