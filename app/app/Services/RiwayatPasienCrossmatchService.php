<?php

namespace App\Services;

use App\Models\PermintaanFpup;
use App\Models\PemberianDarahCrossmatch;
use App\Models\PengembalianDarahCrossmatch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RiwayatPasienCrossmatchService
{
    /**
     * Daftar pasien (group by no_ktp, fallback nama_pasien jika no_ktp kosong)
     * dengan ringkasan total permintaan FPUP & tanggal permintaan terakhir.
     */
    public function searchPasien(?string $keyword = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = PermintaanFpup::query()
            ->select([
                DB::raw('COALESCE(no_ktp, nama_pasien) as pasien_key'),
                'no_ktp',
                'nama_pasien',
                DB::raw('MAX(tgl_minta) as tgl_terakhir'),
                DB::raw('COUNT(*) as total_permintaan'),
            ])
            ->groupBy('pasien_key', 'no_ktp', 'nama_pasien');

        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_pasien', 'like', "%{$keyword}%")
                  ->orWhere('no_ktp', 'like', "%{$keyword}%")
                  ->orWhere('no_fpup', 'like', "%{$keyword}%");
            });
        }

        return $query->orderByDesc('tgl_terakhir')->paginate($perPage)->withQueryString();
    }

    /**
     * Bangun timeline lengkap riwayat crossmatch seorang pasien:
     * Permintaan FPUP -> Cross Test -> Pelayanan Cross Test -> Pemberian Darah -> Pengembalian Darah.
     * Pasien diidentifikasi lewat no_ktp (lebih akurat, unik) atau nama_pasien sebagai fallback.
     */
    public function getRiwayatLengkap(?string $noKtp, ?string $namaPasien): Collection
    {
        $permintaanQuery = PermintaanFpup::query()
            ->with(['crossTests.pelayananCrosstests'])
            ->orderBy('tgl_minta');

        if (!empty($noKtp)) {
            $permintaanQuery->where('no_ktp', $noKtp);
        } elseif (!empty($namaPasien)) {
            $permintaanQuery->where('nama_pasien', $namaPasien);
        } else {
            return collect();
        }

        $permintaanList = $permintaanQuery->get();

        if ($permintaanList->isEmpty()) {
            return collect();
        }

        $noFpupList = $permintaanList->pluck('no_fpup')->unique()->values();

        $pemberianList = PemberianDarahCrossmatch::with('details')
            ->whereIn('no_fpup', $noFpupList)
            ->get()
            ->groupBy('no_fpup');

        $pengembalianList = PengembalianDarahCrossmatch::with('details')
            ->whereIn('no_fpup', $noFpupList)
            ->get()
            ->groupBy('no_fpup');

        $timeline = collect();

        foreach ($permintaanList as $permintaan) {
            $timeline->push([
                'tanggal'    => optional($permintaan->tgl_minta)->format('Y-m-d'),
                'jenis'      => 'Permintaan FPUP',
                'no_fpup'    => $permintaan->no_fpup,
                'status'     => $permintaan->status,
                'keterangan' => trim("Permintaan {$permintaan->jns_permintaan} dari {$permintaan->nama_rs}"),
            ]);

            foreach ($permintaan->crossTests as $crossTest) {
                $timeline->push([
                    'tanggal'    => optional($crossTest->tgl_periksa ?? $crossTest->created_at)->format('Y-m-d H:i'),
                    'jenis'      => 'Cross Test',
                    'no_fpup'    => $permintaan->no_fpup,
                    'status'     => $crossTest->status,
                    'keterangan' => trim("Stock {$crossTest->no_stock} - {$crossTest->jns_darah} {$crossTest->gol_rh_kantong}"),
                ]);

                foreach ($crossTest->pelayananCrosstests as $pelayanan) {
                    $timeline->push([
                        'tanggal'    => optional($pelayanan->tgl_periksa ?? $pelayanan->created_at)->format('Y-m-d H:i'),
                        'jenis'      => 'Pelayanan Cross Test',
                        'no_fpup'    => $permintaan->no_fpup,
                        'status'     => $pelayanan->status,
                        'keterangan' => trim("Hasil: {$pelayanan->hasil} (metode {$pelayanan->metode})"),
                    ]);
                }
            }

            foreach ($pemberianList->get($permintaan->no_fpup, collect()) as $pemberian) {
                $timeline->push([
                    'tanggal'    => optional($pemberian->tanggal)->format('Y-m-d'),
                    'jenis'      => 'Pemberian Darah',
                    'no_fpup'    => $permintaan->no_fpup,
                    'status'     => $pemberian->status,
                    'keterangan' => "{$pemberian->jumlah_kantong} kantong diminta, {$pemberian->dilayani} dilayani",
                ]);
            }

            foreach ($pengembalianList->get($permintaan->no_fpup, collect()) as $pengembalian) {
                $timeline->push([
                    'tanggal'    => optional($pengembalian->tanggal_kembali)->format('Y-m-d'),
                    'jenis'      => 'Pengembalian Darah',
                    'no_fpup'    => $permintaan->no_fpup,
                    'status'     => $pengembalian->status_kembali,
                    'keterangan' => $pengembalian->alasan_kembali ?? '-',
                ]);
            }
        }

        return $timeline->sortBy('tanggal')->values();
    }
}