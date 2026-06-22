<?php

namespace App\Services;

use App\Models\JenisBiaya;
use App\Models\PelayananDarah;
use App\Models\PelayananDarahDetail;
use App\Models\PemberianDarah;
use App\Models\ServiceCost;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PelayananDarahService
{
    // ── Nomor Otomatis ────────────────────────────────────────────────────────

    public function nextNoPelayanan(): string
    {
        $prefix = 'PD-' . now()->format('Ym') . '-';

        $last = PelayananDarah::withTrashed()
            ->where('no_pelayanan', 'like', $prefix . '%')
            ->orderByDesc('no_pelayanan')
            ->value('no_pelayanan');

        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Scan no_fpup / no_pemberian → kembalikan data pasien + detail darah
     * dengan harga_satuan otomatis dari tabel service_costs.
     */
    public function scanPemberian(string $keyword): ?array
    {
        $keyword = trim($keyword);

        // 1. Cari pemberian_darah + eager load relasi yang dibutuhkan (case-insensitive)
        $pemberian = PemberianDarah::with([
                'detail',           // pemberian_darah_detail  (relasi di model PemberianDarah)
                'permintaanFpup',   // permintaan_fpup
            ])
            ->where(function ($q) use ($keyword) {
                $q->whereRaw('LOWER(no_fpup) = ?', [mb_strtolower($keyword)])
                  ->orWhereRaw('LOWER(no_pemberian) = ?', [mb_strtolower($keyword)]);
            })
            ->first();

        if (! $pemberian) {
            return null;
        }

        // 2. Ambil data permintaan_fpup (informasi pasien & RS) — nullable
        $fpup = $pemberian->permintaanFpup;

        // 3. Ambil semua jenis_biaya untuk dropdown (di-trim karena data 'nama' di DB
        //    sering tercemar \r\n / spasi tersembunyi dari import lama)
        $jenisBiayaList = $this->getJenisBiayaList();

        // 4. Nama jenis biaya — jadi acuan pencarian harga di service_costs
        //    Di-trim supaya konsisten dengan $jenisBiayaList (lihat poin 3) dan
        //    supaya <select> di frontend bisa match persis dengan opsinya.
        $jnsBiayaNama = trim(
            $fpup?->jenisBiaya?->nama
                ?? $fpup?->jns_biaya
                ?? $pemberian->jns_biaya
                ?? ''
        ) ?: null;

        // 4b. Jenis RS (Pemerintah/Swasta) — dipakai bareng jns_biaya untuk cari
        //     harga di service_costs. PENTING: harga di tabel service_costs
        //     ditentukan oleh kombinasi (Jenis RS × Jenis Biaya), BUKAN oleh
        //     jenis darah (WB/PRC/dst) — lihat kolom 'jenis' di service_costs
        //     yang isinya "Pemerintah"/"Swasta", bukan tipe darah.
        $jenisRs = trim($fpup?->jenis_rs ?? $pemberian->jenis_rs ?? '') ?: null;

        // 4c. Harga otomatis — SATU nilai untuk seluruh pelayanan ini, karena
        //     biayanya per kombinasi RS+biaya, sama untuk semua baris darah.
        $hargaOtomatis = $this->getHargaSatuan($jenisRs, $jnsBiayaNama);

        // 5. Susun detail baris dari pemberian_darah_detail + harga otomatis
        $details = $pemberian->detail->map(function ($d) use ($hargaOtomatis) {
            return [
                'id'                        => $d->id,
                'pemberian_darah_detail_id' => $d->id,
                'no_stok'                   => $d->no_stok,
                'jns_darah'                 => $d->jns_darah,
                'gol'                       => $d->gol,
                'rhesus'                    => $d->rhesus,
                'jumlah'                    => $d->jumlah ?? 1,
                'cc'                        => $d->cc,
                // Harga sama untuk semua baris (per kombinasi Jenis RS + Jenis Biaya);
                // fallback ke harga di detail kalau tidak ketemu di service_costs
                'harga_satuan'              => $hargaOtomatis > 0
                                                    ? $hargaOtomatis
                                                    : (float) ($d->harga_satuan ?? 0),
            ];
        })->values()->toArray();

        // 6. Susun respons — prioritaskan permintaan_fpup untuk data pasien & RS
        //    Gunakan null-safe operator (?->) supaya tidak error saat $fpup null.
        return [
            // ── Identitas pemberian ──────────────────────────────────────────
            'id'             => $pemberian->id,
            'no_fpup'        => $pemberian->no_fpup,
            'no_pemberian'   => $pemberian->no_pemberian,

            // ── Data pasien (dari permintaan_fpup jika ada, fallback ke pemberian_darah) ──
            'nama_pasien'    => $fpup?->nama_os    ?? $pemberian->nama_pasien   ?? null,
            'no_register'    => $fpup?->no_register ?? null,
            'golongan_darah' => $this->parseGol($fpup?->gol_rh_os ?? $pemberian->gol_rh_pasien ?? null),
            'rhesus'         => $this->parseRhesus($fpup?->gol_rh_os ?? $pemberian->gol_rh_pasien ?? null),
            'alamat_os'      => $fpup?->alamat_os  ?? null,

            // ── Data RS (dari permintaan_fpup jika ada, fallback ke pemberian_darah) ──
            'nama_rs'        => $fpup?->nama_rs    ?? $pemberian->nama_rs    ?? null,
            'kode_rs'        => $fpup?->kode_rs    ?? $pemberian->kode_rs    ?? null,
            'jenis_rs'       => $fpup?->jenis_rs   ?? $pemberian->jenis_rs   ?? null,
            'bagian_rs'      => $fpup?->bagian      ?? null,   // kolom 'bagian' di permintaan_fpup
            'kelas_rawat'    => $fpup?->kelas_rawat ?? $pemberian->kelas_rawat ?? null,
            'nama_dokter'    => $fpup?->nama_dokter ?? $pemberian->nama_dokter ?? null,

            // ── Pembayaran ────────────────────────────────────────────────────
            'cara_bayar'     => $fpup?->cara_bayar  ?? $pemberian->cara_pembayaran ?? null,
            'jns_biaya'      => $jnsBiayaNama,

            // ── Dropdown jenis biaya ──────────────────────────────────────────
            'jenis_biaya_list' => $jenisBiayaList,

            // ── Detail baris darah (sudah berisi harga_satuan otomatis) ───────
            'details'        => $details,
        ];
    }

    /**
     * Bersihkan kolom dari karakter tersembunyi (\r \n) + spasi berlebih DI LEVEL SQL.
     * PENTING: TRIM() bawaan MySQL HANYA membuang spasi, TIDAK membuang \r / \n —
     * beda dengan trim() PHP. Makanya harus di-REPLACE() dulu sebelum di-TRIM().
     */
    private function sqlClean(string $column): string
    {
        return "TRIM(REPLACE(REPLACE({$column}, CHAR(13), ''), CHAR(10), ''))";
    }

    /**
     * Cari harga satuan otomatis dari tabel service_costs.
     *
     * PENTING — temuan dari data aktual: kolom 'jenis' di service_costs berisi
     * "Pemerintah" / "Swasta" (tipe rumah sakit), BUKAN tipe darah. Dan kolom
     * 'nama' adalah kode gabungan: prefix tipe RS + suffix kode jenis biaya,
     * contoh "PWBNATBPJS" (Pemerintah + NATBPJS), "SKOBPJSPRI" (Swasta + BPJSPRI).
     * Jadi harga ditentukan oleh kombinasi (Jenis RS × Jenis Biaya), sama untuk
     * semua baris darah dalam satu pelayanan — bukan per jenis darah (WB/PRC/dst).
     *
     * Kolom 'jenis_biaya_id' TIDAK dipakai sebagai filter karena tidak reliable
     * (banyak baris ter-default ke nilai yang sama, tidak mencerminkan kode di 'nama').
     */
    public function getHargaSatuan(?string $jenisRs, ?string $jnsBiayaNama = null): float
    {
        if (! $jnsBiayaNama) {
            return 0;
        }

        $jnsBiayaNama = trim($jnsBiayaNama);
        $jenisRs      = $jenisRs ? trim($jenisRs) : null;

        // Kode jenis biaya (mis. "NATBPJS") dicari sebagai SUBSTRING di kolom 'nama'
        $buildQuery = function () use ($jnsBiayaNama) {
            return ServiceCost::whereRaw(
                $this->sqlClean('nama') . ' LIKE ?',
                ["%{$jnsBiayaNama}%"]
            );
        };

        // 1) Persempit dengan Jenis RS (Pemerintah/Swasta) — paling akurat
        $cost = null;
        if ($jenisRs) {
            $cost = $buildQuery()
                ->whereRaw('LOWER(' . $this->sqlClean('jenis') . ') = ?', [mb_strtolower($jenisRs)])
                ->orderByDesc('id')
                ->first();

            // partial match kalau exact tidak ketemu (jaga-jaga beda penulisan)
            if (! $cost) {
                $cost = $buildQuery()
                    ->whereRaw('LOWER(' . $this->sqlClean('jenis') . ') LIKE ?', ['%' . mb_strtolower($jenisRs) . '%'])
                    ->orderByDesc('id')
                    ->first();
            }
        }

        // 2) Fallback: tanpa filter Jenis RS (kombinasi spesifik tidak ketemu / RS kosong)
        if (! $cost) {
            $cost = $buildQuery()->orderByDesc('id')->first();
        }

        return (float) ($cost->biaya ?? 0);
    }

    /**
     * Ambil hanya daftar jenis_biaya (untuk populate dropdown saat modal dibuka).
     * 'nama' & 'kode' di-trim karena data lama sering tercemar \r\n / spasi
     * tersembunyi (kelihatan kalau di-dump JSON: "BPJS\r\n" dst).
     */
    public function getJenisBiayaList(): array
    {
        return JenisBiaya::orderBy('nama')
            ->get(['id', 'kode', 'nama'])
            ->map(fn ($jb) => [
                'id'   => $jb->id,
                'kode' => trim($jb->kode),
                'nama' => trim($jb->nama),
            ])
            ->toArray();
    }

    // ── List / Filter ─────────────────────────────────────────────────────────

    public function getData(array $filters = [])
    {
        $query = PelayananDarah::with('details')
            ->orderByDesc('tgl_pelayanan');

        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('no_pelayanan', 'like', "%{$filters['search']}%")
                  ->orWhere('no_fpup',     'like', "%{$filters['search']}%")
                  ->orWhere('nama_pasien', 'like', "%{$filters['search']}%")
                  ->orWhere('no_register', 'like', "%{$filters['search']}%");
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['dari']) && ! empty($filters['sampai'])) {
            $query->whereBetween('tgl_pelayanan', [$filters['dari'], $filters['sampai']]);
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    public function store(array $data): PelayananDarah
    {
        return DB::transaction(function () use ($data) {

            $header = PelayananDarah::create([
                'no_pelayanan'       => $this->nextNoPelayanan(),
                'pemberian_darah_id' => $data['pemberian_darah_id'] ?? null,
                'no_pemberian'       => $data['no_pemberian']       ?? null,
                'no_fpup'            => $data['no_fpup']            ?? null,
                'tgl_fpup'           => $data['tgl_fpup'],
                'tgl_pelayanan'      => $data['tgl_pelayanan']      ?? now()->toDateString(),
                'jam_pelayanan'      => $data['jam_pelayanan']      ?? now()->format('H:i'),
                'cara_bayar'         => $data['cara_bayar']         ?? null,
                'jns_biaya'          => $data['jns_biaya']          ?? null,
                'no_register'        => $data['no_register']        ?? null,
                'no_faktur'          => $data['no_faktur']          ?? null,
                'nama_pasien'        => $data['nama_pasien']        ?? null,
                'nama_dokter'        => $data['nama_dokter']        ?? null,
                'nama_rs'            => $data['nama_rs']            ?? null,
                'kode_rs'            => $data['kode_rs']            ?? null,
                'jenis_rs'           => $data['jenis_rs']           ?? null,
                'bagian_rs'          => $data['bagian_rs']          ?? null,
                'kelas_rawat'        => $data['kelas_rawat']        ?? null,
                'golongan_darah'     => $data['golongan_darah']     ?? null,
                'rhesus'             => $data['rhesus']             ?? null,
                'alamat_os'          => $data['alamat_os']          ?? null,
                'total_biaya'        => $data['total_biaya']        ?? 0,
                'diskon'             => $data['diskon']             ?? 0,
                'total_bayar'        => $data['total_bayar']        ?? 0,
                'terbayar'           => $data['terbayar']           ?? 0,
                'kembalian'          => $data['kembalian']          ?? 0,
                'status'             => 'baru',
                'petugas_kasir'      => Auth::user()?->name,
                'keterangan'         => $data['keterangan']         ?? null,
                'cara_pembayaran'    => $data['cara_pembayaran']    ?? null,
            ]);

            $this->syncDetails($header, $data['details'] ?? []);

            return $header->load('details');
        });
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function update(PelayananDarah $pelayanan, array $data): PelayananDarah
    {
        return DB::transaction(function () use ($pelayanan, $data) {

            $pelayanan->update([
                'no_pemberian'    => $data['no_pemberian']    ?? $pelayanan->no_pemberian,
                'no_fpup'         => $data['no_fpup']         ?? $pelayanan->no_fpup,
                'tgl_fpup'        => $data['tgl_fpup']        ?? $pelayanan->tgl_fpup,
                'tgl_pelayanan'   => $data['tgl_pelayanan']   ?? $pelayanan->tgl_pelayanan,
                'jam_pelayanan'   => $data['jam_pelayanan']   ?? $pelayanan->jam_pelayanan,
                'cara_bayar'      => $data['cara_bayar']      ?? $pelayanan->cara_bayar,
                'jns_biaya'       => $data['jns_biaya']       ?? $pelayanan->jns_biaya,
                'no_register'     => $data['no_register']     ?? $pelayanan->no_register,
                'no_faktur'       => $data['no_faktur']       ?? $pelayanan->no_faktur,
                'nama_pasien'     => $data['nama_pasien']     ?? $pelayanan->nama_pasien,
                'nama_dokter'     => $data['nama_dokter']     ?? $pelayanan->nama_dokter,
                'nama_rs'         => $data['nama_rs']         ?? $pelayanan->nama_rs,
                'kode_rs'         => $data['kode_rs']         ?? $pelayanan->kode_rs,
                'jenis_rs'        => $data['jenis_rs']        ?? $pelayanan->jenis_rs,
                'bagian_rs'       => $data['bagian_rs']       ?? $pelayanan->bagian_rs,
                'kelas_rawat'     => $data['kelas_rawat']     ?? $pelayanan->kelas_rawat,
                'golongan_darah'  => $data['golongan_darah']  ?? $pelayanan->golongan_darah,
                'rhesus'          => $data['rhesus']          ?? $pelayanan->rhesus,
                'alamat_os'       => $data['alamat_os']       ?? $pelayanan->alamat_os,
                'total_biaya'     => $data['total_biaya']     ?? $pelayanan->total_biaya,
                'diskon'          => $data['diskon']          ?? $pelayanan->diskon,
                'total_bayar'     => $data['total_bayar']     ?? $pelayanan->total_bayar,
                'terbayar'        => $data['terbayar']        ?? $pelayanan->terbayar,
                'kembalian'       => $data['kembalian']       ?? $pelayanan->kembalian,
                'keterangan'      => $data['keterangan']      ?? $pelayanan->keterangan,
                'cara_pembayaran' => $data['cara_pembayaran'] ?? $pelayanan->cara_pembayaran,
            ]);

            if (isset($data['details'])) {
                $this->syncDetails($pelayanan, $data['details']);
            }

            return $pelayanan->load('details');
        });
    }

    // ── Update Status ─────────────────────────────────────────────────────────

    public function updateStatus(PelayananDarah $pelayanan, string $status): PelayananDarah
    {
        if (! in_array($status, ['baru', 'lunas', 'batal'])) {
            throw new \InvalidArgumentException("Status '{$status}' tidak valid.");
        }

        $pelayanan->update(['status' => $status]);

        return $pelayanan;
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function destroy(PelayananDarah $pelayanan): void
    {
        DB::transaction(function () use ($pelayanan) {
            $pelayanan->details()->delete();
            $pelayanan->delete();
        });
    }

    // ── Sync Detail ───────────────────────────────────────────────────────────

    private function syncDetails(PelayananDarah $pelayanan, array $details): void
    {
        $pelayanan->details()->delete();

        $totalBiaya = 0;

        foreach ($details as $row) {
            $jumlah     = (int)   ($row['jumlah']       ?? 1);
            $harga      = (float) ($row['harga_satuan'] ?? 0);
            $totalHarga = $jumlah * $harga;

            PelayananDarahDetail::create([
                'pelayanan_darah_id'        => $pelayanan->id,
                'pemberian_darah_detail_id' => $row['pemberian_darah_detail_id'] ?? null,
                'no_stok'                   => $row['no_stok']      ?? null,
                'jns_darah'                 => $row['jns_darah']    ?? null,
                'gol'                       => $row['gol']          ?? null,
                'rhesus'                    => $row['rhesus']       ?? null,
                'jumlah'                    => $jumlah,
                'cc'                        => $row['cc']           ?? null,
                'harga_satuan'              => $harga,
                'total_harga'               => $totalHarga,
                'keterangan'                => $row['keterangan']   ?? null,
            ]);

            $totalBiaya += $totalHarga;
        }

        $pelayanan->update(['total_biaya' => $totalBiaya]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Parse golongan darah dari string gabungan seperti "A+" / "O Positif" / "AB-"
     * Kembalikan hanya huruf golongan: A / B / AB / O
     */
    private function parseGol(?string $golRh): ?string
    {
        if (! $golRh) return null;
        if (preg_match('/\b(AB|A|B|O)\b/i', strtoupper($golRh), $m)) {
            return strtoupper($m[1]);
        }
        return null;
    }

    /**
     * Parse rhesus dari string gabungan.
     * Kembalikan "+" atau "-"
     */
    private function parseRhesus(?string $golRh): ?string
    {
        if (! $golRh) return null;
        if (str_contains(strtolower($golRh), 'positif') || str_contains($golRh, '+')) return '+';
        if (str_contains(strtolower($golRh), 'negatif') || str_contains($golRh, '-')) return '-';
        return null;
    }
}