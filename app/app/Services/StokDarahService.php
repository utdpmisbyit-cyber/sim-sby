<?php

namespace App\Services;

use App\Models\StokDarah;
use App\Models\TransaksiStokDarah;
use App\Models\PenerimaanProlisPenyimpanan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StokDarahService
{
    /**
     * Dipanggil saat penerimaan disimpan (store).
     * Buat/update record stok_darah dan catat transaksi MASUK.
     */
      public function masuk(PenerimaanProlisPenyimpanan $penerimaan): StokDarah
    {
        return DB::transaction(function () use ($penerimaan) {

            $jumlah = $penerimaan->jumlah ?? 1;

            \Log::info('StokDarahService::masuk()', [
                'no_stok'      => $penerimaan->no_stok,
                'penerimaan_id'=> $penerimaan->id,
                'jumlah'       => $jumlah,
            ]);

            $stok = StokDarah::updateOrCreate(
                ['no_stok' => $penerimaan->no_stok],
                [
                    'no_kantong'     => $penerimaan->no_kantong,
                    'jenis_darah'    => $penerimaan->jenis_darah,
                    'golongan_darah' => $penerimaan->golongan_darah,
                    'rhesus'         => $penerimaan->rhesus,
                    'tgl_aftap'      => $penerimaan->tgl_aftap,
                    'tgl_produksi'   => $penerimaan->tgl_produksi,
                    'tgl_expired'    => $penerimaan->tgl_expired,
                    'ruang'          => $penerimaan->ruang,
                    'ml'             => $penerimaan->ml,
                    'gr'             => $penerimaan->gr,
                    'skrining'       => $penerimaan->skrining,
                    'no_fpd'         => $penerimaan->no_fpd,
                    'asal_darah_id'  => $penerimaan->asal_darah_id,
                    'penerimaan_id'  => $penerimaan->id,
                    'status_stok'    => 'tersedia',
                ]
            );

            $stok->increment('jumlah_masuk', $jumlah);
            $stok->refresh();
            $stok->saldo = $stok->jumlah_masuk
                        - $stok->jumlah_keluar
                        + $stok->jumlah_kembali;
            $stok->save();

            TransaksiStokDarah::create([
                'no_stok'       => $penerimaan->no_stok,
                'stok_darah_id' => $stok->id,
                'jenis'         => 'masuk',
                'jumlah'        => $jumlah,
                'no_referensi'  => $penerimaan->no_penerimaan,
                'sumber'        => 'penerimaan',
                'referensi_id'  => $penerimaan->id,
                'keterangan'    => 'Penerimaan prolis penyimpanan',
                'petugas_id'    => $penerimaan->petugas_id,
                'created_by'    => $penerimaan->created_by,
            ]);

            \Log::info('StokDarahService::masuk() selesai', [
                'stok_id' => $stok->id,
                'saldo'   => $stok->saldo,
            ]);

            return $stok->fresh();
        });
    }

    /**
     * Dipanggil saat pengiriman (internal/eksternal) disimpan.
     * Kurangi saldo, catat transaksi KELUAR.
     *
     * @param  string  $noStok
     * @param  int     $jumlah
     * @param  string  $noReferensi   no_pengiriman / no_permintaan
     * @param  string  $sumber        'pengiriman_internal' | 'pengiriman_external'
     * @param  int     $referensiId
     */
    public function keluar(
        string $noStok,
        int    $jumlah,
        string $noReferensi,
        string $sumber,
        int    $referensiId
    ): StokDarah {
        return DB::transaction(function ()
            use ($noStok, $jumlah, $noReferensi, $sumber, $referensiId)
        {
            $stok = StokDarah::where('no_stok', $noStok)->lockForUpdate()->firstOrFail();

            if ($stok->saldo < $jumlah) {
                throw new \Exception("Stok {$noStok} tidak mencukupi. Saldo: {$stok->saldo}");
            }

            $stok->increment('jumlah_keluar', $jumlah);
            $stok->saldo = $stok->jumlah_masuk
                         - $stok->jumlah_keluar
                         + $stok->jumlah_kembali;

            if ($stok->saldo <= 0) {
                $stok->status_stok = 'dipakai';
            }

            $stok->save();

            TransaksiStokDarah::create([
                'no_stok'       => $noStok,
                'stok_darah_id' => $stok->id,
                'jenis'         => 'keluar',
                'jumlah'        => $jumlah,
                'no_referensi'  => $noReferensi,
                'sumber'        => $sumber,
                'referensi_id'  => $referensiId,
                'keterangan'    => "Keluar via {$sumber}",
                'petugas_id'    => Auth::id(),
                'created_by'    => Auth::id(),
            ]);

            return $stok->fresh();
        });
    }

    /**
     * Dipanggil saat pengembalian darah.
     * Tambah saldo kembali, catat transaksi KEMBALI.
     */
    public function kembali(
        string $noStok,
        int    $jumlah,
        string $noReferensi,
        string $sumber,
        int    $referensiId,
        string $keterangan = 'Pengembalian darah'
    ): StokDarah {
        return DB::transaction(function ()
            use ($noStok, $jumlah, $noReferensi, $sumber, $referensiId, $keterangan)
        {
            $stok = StokDarah::where('no_stok', $noStok)->lockForUpdate()->firstOrFail();

            $stok->increment('jumlah_kembali', $jumlah);
            $stok->saldo = $stok->jumlah_masuk
                         - $stok->jumlah_keluar
                         + $stok->jumlah_kembali;
            $stok->status_stok = 'tersedia';
            $stok->save();

            TransaksiStokDarah::create([
                'no_stok'       => $noStok,
                'stok_darah_id' => $stok->id,
                'jenis'         => 'kembali',
                'jumlah'        => $jumlah,
                'no_referensi'  => $noReferensi,
                'sumber'        => $sumber,
                'referensi_id'  => $referensiId,
                'keterangan'    => $keterangan,
                'petugas_id'    => Auth::id(),
                'created_by'    => Auth::id(),
            ]);

            return $stok->fresh();
        });
    }

    /**
     * Dipanggil saat penerimaan dihapus (soft-delete).
     * Rollback jumlah_masuk dan saldo.
     */
    public function rollbackMasuk(PenerimaanProlisPenyimpanan $penerimaan): void
    {
        DB::transaction(function () use ($penerimaan) {

            $stok = StokDarah::where('no_stok', $penerimaan->no_stok)
                             ->lockForUpdate()
                             ->first();

            if (!$stok) return;

            $jumlah = $penerimaan->jumlah ?? 1;

            $stok->jumlah_masuk = max(0, $stok->jumlah_masuk - $jumlah);
            $stok->saldo = $stok->jumlah_masuk
                         - $stok->jumlah_keluar
                         + $stok->jumlah_kembali;

            if ($stok->saldo <= 0 && $stok->jumlah_masuk === 0) {
                $stok->status_stok = 'dibuang';
            }

            $stok->save();

            TransaksiStokDarah::create([
                'no_stok'       => $penerimaan->no_stok,
                'stok_darah_id' => $stok->id,
                'jenis'         => 'hapus',
                'jumlah'        => $jumlah,
                'no_referensi'  => $penerimaan->no_penerimaan,
                'sumber'        => 'penerimaan',
                'referensi_id'  => $penerimaan->id,
                'keterangan'    => 'Rollback: penerimaan dihapus',
                'petugas_id'    => Auth::id(),
                'created_by'    => Auth::id(),
            ]);
        });
    }

    
    public function getAll(array $filters = [])
    {
        $q = StokDarah::query();

        if (!empty($filters['jenis_darah']))    $q->where('jenis_darah', $filters['jenis_darah']);
        if (!empty($filters['golongan_darah'])) $q->where('golongan_darah', $filters['golongan_darah']);
        if (!empty($filters['rhesus']))         $q->where('rhesus', $filters['rhesus']);
        if (!empty($filters['ruang']))          $q->where('ruang', $filters['ruang']);
        if (!empty($filters['status_stok']))    $q->where('status_stok', $filters['status_stok']);
        if (!empty($filters['no_stok']))        $q->where('no_stok', 'like', '%'.$filters['no_stok'].'%');

        return $q->orderByDesc('id')->get();
    }

    public function getTransaksiByNoStok(string $noStok)
    {
        return TransaksiStokDarah::where('no_stok', $noStok)
            ->with('petugas')
            ->orderByDesc('id')
            ->get();
    }

    public function getSummaryByRuang(string $ruang): array
    {
        $rows = StokDarah::where('ruang', $ruang)->get();

        return [
            'ruang'          => $ruang,
            'total_masuk'    => $rows->sum('jumlah_masuk'),
            'total_keluar'   => $rows->sum('jumlah_keluar'),
            'total_kembali'  => $rows->sum('jumlah_kembali'),
            'saldo'          => $rows->sum('saldo'),
            'tersedia'       => $rows->where('status_stok', 'tersedia')->count(),
            'dipakai'        => $rows->where('status_stok', 'dipakai')->count(),
        ];
    }
}