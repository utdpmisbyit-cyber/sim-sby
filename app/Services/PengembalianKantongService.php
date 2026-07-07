<?php

namespace App\Services;

use App\Models\PengembalianKantong;
use App\Models\PengembalianKantongDetail;
use Illuminate\Support\Facades\DB;

class PengembalianKantongService extends IoService
{
    /**
     * Nama tabel sumber stok fisik kantong (per-serial, hasil alur
     * penerimaan → sample → serologi). Stok "tersedia" dihitung dari
     * sini, BUKAN dari stok_kantong_masuk, karena stok_kantong_masuk
     * tidak melacak per-nomor kantong.
     */
    protected string $tableStokDetail = 'stok_kantong_penerimaan_detail';

    public function __construct()
    {
        $this->model   = new PengembalianKantong();
        $this->sort_by = [
            'tgl_kembali' => 'desc',
            'no_kembali'  => 'desc'
        ];

        $this->filters = [
            'no_kembali',
            'no_kantong',
            'kondisi',
            'tgl_kembali',
            'asal_darah',
        ];

        $this->with = [
            'stokKantong',
            'asalDarah',
            'details.tipe_kantong'
        ];
    }

    public function search($params = [])
    {
        $query = PengembalianKantong::query()
            ->with($this->with);

        $query = $this->dynamic_search($query, $params);

        return $query
            ->orderBy('tgl_kembali', 'desc')
            ->orderBy('no_kembali', 'desc')
            ->paginate($params['per_page'] ?? 10)
            ->withQueryString();
    }

    public function dynamic_search($model, $params = [])
    {
        $no_kembali  = $params['no_kembali'] ?? '';
        $no_kantong  = $params['no_kantong'] ?? '';
        $kondisi     = $params['kondisi'] ?? '';
        $tgl_kembali = $params['tgl_kembali'] ?? '';
        $asal_darah  = $params['asal_darah'] ?? '';   // ← text, bukan id

        if ($no_kembali !== '') {
            $model->where('no_kembali', 'like', '%' . $no_kembali . '%');
        }

        if ($no_kantong !== '') {
            $model->where('no_kantong', 'like', '%' . $no_kantong . '%');
        }

        if ($kondisi !== '') {
            $model->where('kondisi', $kondisi);
        }

        if ($tgl_kembali !== '') {
            $model->whereDate('tgl_kembali', $tgl_kembali);
        }

        if ($asal_darah !== '') {
            $model->whereHas('asalDarah', function ($q) use ($asal_darah) {
                $q->where('nama', 'like', '%' . $asal_darah . '%');
            });
        }

        return $model;
    }

    /**
     * Generate no_kembali
     * KB + YYMM + 6 digit sequence
     * ex: KB2505000001
     */
    public function generateNoKembali(): string
    {
        $prefix = 'KB' . now()->format('ym');

        $last = PengembalianKantong::where(
            'no_kembali',
            'like',
            $prefix . '%'
        )
            ->orderByDesc('no_kembali')
            ->value('no_kembali');

        $seq = $last
            ? ((int) substr($last, strlen($prefix))) + 1
            : 1;

        return $prefix . str_pad(
            $seq,
            6,
            '0',
            STR_PAD_LEFT
        );
    }

    /**
     * ──────────────────────────────────────────────────────────
     * HITUNG SISA STOK OTOMATIS untuk 1 kombinasi merk+jenis+ukuran.
     *
     * jumlah_tersedia_saat_ini = COUNT baris di stok_kantong_penerimaan_detail
     *   dengan merk/jenis/ukuran yang sama DAN status_kirim = 'tersedia'.
     *
     * sisa = jumlah_tersedia_saat_ini - total yang SUDAH dicatat
     *        dikembalikan (pengembalian_kantong_detail) untuk kombinasi
     *        merk+jenis+ukuran yang sama.
     *
     * $excludePengembalianId dipakai saat mode EDIT, supaya jumlah
     * yang sudah tercatat di record itu sendiri tidak ikut dianggap
     * "terpakai" (karena sedang diedit ulang).
     * ──────────────────────────────────────────────────────────
     */
    public function getSisaStok(?string $merk, ?string $jenis, ?string $ukuran, ?int $excludePengembalianId = null): array
    {
        $jumlahTersedia = DB::table($this->tableStokDetail)
            ->where('merk', $merk)
            ->where('jenis', $jenis)
            ->where('ukuran', $ukuran)
            ->where('status_kirim', 'tersedia')
            ->count();

        $sudahDikembalikan = (int) PengembalianKantongDetail::query()
            ->whereHas('pengembalian_kantong', function ($q) use ($merk, $jenis, $ukuran, $excludePengembalianId) {
                $q->where('merk', $merk)
                  ->where('jenis', $jenis)
                  ->where('ukuran', $ukuran);
                if ($excludePengembalianId) {
                    $q->where('id', '!=', $excludePengembalianId);
                }
            })
            ->sum('jumlah');

        $sisa = max(0, $jumlahTersedia - $sudahDikembalikan);

        return [
            'jumlah_tersedia_saat_ini' => $jumlahTersedia,
            'sudah_dikembalikan'       => $sudahDikembalikan,
            'sisa'                     => $sisa,
        ];
    }

    /**
     * Ambil 1 baris kantong fisik dari stok_kantong_penerimaan_detail
     * berdasarkan no_kantong. Ini sumber data merk/jenis/ukuran yang
     * dipakai untuk hitung sisa stok saat scan.
     */
    public function findKantongFisik(string $noKantong): ?object
    {
        return DB::table($this->tableStokDetail)
            ->where('no_kantong', $noKantong)
            ->first();
    }

    /**
     * ──────────────────────────────────────────────────────────
     * VALIDASI total jumlah yang mau disimpan tidak melebihi sisa
     * stok riil. Dipanggil di dalam DB::transaction() dengan lock
     * baris-baris terkait supaya aman dari race condition
     * (dua user input bareng untuk kombinasi merk/jenis/ukuran sama).
     *
     * @throws \Exception
     * ──────────────────────────────────────────────────────────
     */
    public function assertJumlahValid(?string $merk, ?string $jenis, ?string $ukuran, int $totalJumlahBaru, ?int $excludePengembalianId = null): void
    {
        // Lock baris-baris stok terkait supaya perhitungan akurat saat konkuren
        $jumlahTersedia = DB::table($this->tableStokDetail)
            ->where('merk', $merk)
            ->where('jenis', $jenis)
            ->where('ukuran', $ukuran)
            ->where('status_kirim', 'tersedia')
            ->lockForUpdate()
            ->count();

        $sudahDikembalikan = (int) PengembalianKantongDetail::query()
            ->whereHas('pengembalian_kantong', function ($q) use ($merk, $jenis, $ukuran, $excludePengembalianId) {
                $q->where('merk', $merk)
                  ->where('jenis', $jenis)
                  ->where('ukuran', $ukuran);
                if ($excludePengembalianId) {
                    $q->where('id', '!=', $excludePengembalianId);
                }
            })
            ->sum('jumlah');

        $sisa = $jumlahTersedia - $sudahDikembalikan;

        if ($totalJumlahBaru > $sisa) {
            throw new \Exception(
                "Jumlah yang dikembalikan ({$totalJumlahBaru}) melebihi sisa stok yang tersedia ({$sisa}) untuk Merk {$merk} / Jenis {$jenis} / Ukuran {$ukuran}."
            );
        }
    }
}