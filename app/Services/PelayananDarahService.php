<?php

namespace App\Services;

use App\Models\JenisBiaya;
use App\Models\PelayananDarah;
use App\Models\PelayananDarahDetail;
use App\Models\PemberianDarah;
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

   
    public function scanPemberian(string $keyword): ?array
    {
        // 1. Cari pemberian_darah + eager load relasi yang dibutuhkan
        $pemberian = PemberianDarah::with([
                'detail',           // pemberian_darah_detail  (relasi di model PemberianDarah)
                'permintaanFpup',   // permintaan_fpup
            ])
            ->where('no_fpup', $keyword)
            ->orWhere('no_pemberian', $keyword)
            ->first();

        if (! $pemberian) {
            return null;
        }

        // 2. Ambil data permintaan_fpup (informasi pasien & RS)
        $fpup = $pemberian->permintaanFpup; // nullable

        // 3. Ambil semua jenis_biaya untuk dropdown
        $jenisBiayaList = JenisBiaya::orderBy('nama')
            ->get(['id', 'kode', 'nama'])
            ->toArray();

        // 4. Susun detail baris dari pemberian_darah_detail
        $details = $pemberian->detail->map(function ($d) {
            return [
                'id'                       => $d->id,  // pemberian_darah_detail_id
                'pemberian_darah_detail_id'=> $d->id,
                'no_stok'                  => $d->no_stok,
                'jns_darah'                => $d->jns_darah,
                'gol'                      => $d->gol,
                'rhesus'                   => $d->rhesus,
                'jumlah'                   => $d->jumlah ?? 1,
                'cc'                       => $d->cc,
                'harga_satuan'             => (float) ($d->harga_satuan ?? 0),
            ];
        })->values()->toArray();

        // 5. Susun respons — prioritaskan permintaan_fpup untuk data pasien & RS
        return [
            // ── Identitas pemberian ──────────────────────────────────────────
            'id'             => $pemberian->id,
            'no_fpup'        => $pemberian->no_fpup,
            'no_pemberian'   => $pemberian->no_pemberian,

            // ── Data pasien (dari permintaan_fpup jika ada, fallback ke pemberian_darah) ──
            'nama_pasien'    => $fpup->nama_os    ?? $pemberian->nama_pasien   ?? null,
            'no_register'    => $fpup->no_register ?? null,
            'golongan_darah' => $this->parseGol($fpup->gol_rh_os ?? $pemberian->gol_rh_pasien ?? null),
            'rhesus'         => $this->parseRhesus($fpup->gol_rh_os ?? $pemberian->gol_rh_pasien ?? null),
            'alamat_os'      => $fpup->alamat_os  ?? null,

            // ── Data RS (dari permintaan_fpup jika ada, fallback ke pemberian_darah) ──
            'nama_rs'        => $fpup->nama_rs    ?? $pemberian->nama_rs    ?? null,
            'kode_rs'        => $fpup->kode_rs    ?? $pemberian->kode_rs    ?? null,
            'jenis_rs'       => $fpup->jenis_rs   ?? $pemberian->jenis_rs   ?? null,
            'bagian_rs'      => $fpup->bagian      ?? null,   // kolom 'bagian' di permintaan_fpup
            'kelas_rawat'    => $fpup->kelas_rawat ?? $pemberian->kelas_rawat ?? null,
            'nama_dokter'    => $fpup->nama_dokter ?? $pemberian->nama_dokter ?? null,

            // ── Pembayaran ────────────────────────────────────────────────────
            'cara_bayar'     => $fpup->cara_bayar  ?? $pemberian->cara_pembayaran ?? null,
            // jns_biaya sebagai teks (nama dari jenis_biaya jika ada relasi)
            'jns_biaya'      => $fpup->jenisBiaya->nama ?? $fpup->jns_biaya ?? $pemberian->jns_biaya ?? null,

            // ── Dropdown jenis biaya ──────────────────────────────────────────
            'jenis_biaya_list' => $jenisBiayaList,

            // ── Detail baris darah ────────────────────────────────────────────
            'details'        => $details,
        ];
    }

    /**
     * Ambil hanya daftar jenis_biaya (untuk populate dropdown saat modal dibuka).
     */
    public function getJenisBiayaList(): array
    {
        return JenisBiaya::orderBy('nama')
            ->get(['id', 'kode', 'nama'])
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