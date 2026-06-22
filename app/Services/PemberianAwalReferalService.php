<?php

namespace App\Services;

use App\Models\PemberianAwalReferal;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PemberianAwalReferalService
{
    /**
     * Daftar pemberian awal referal dengan pencarian & filter status/tanggal.
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return PemberianAwalReferal::query()
            ->cari($filters['cari'] ?? null)
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['tgl_dari'] ?? null, fn ($q, $tgl) => $q->whereDate('tgl_fpup', '>=', $tgl))
            ->when($filters['tgl_sampai'] ?? null, fn ($q, $tgl) => $q->whereDate('tgl_fpup', '<=', $tgl))
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Buat nomor pemberian baru, format: PA-YYYYMMDD-0001
     */
    public function generateNoPemberian(): string
    {
        $prefix = 'PA-'.now()->format('Ymd').'-';

        $last = PemberianAwalReferal::withTrashed()
            ->where('no_pemberian', 'like', "{$prefix}%")
            ->orderByDesc('no_pemberian')
            ->value('no_pemberian');

        $urutan = $last ? ((int) Str::afterLast($last, '-') + 1) : 1;

        return $prefix.str_pad((string) $urutan, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Cari data FPUP berdasarkan nomor untuk mengisi header form secara otomatis
     * (scan/ketik No FPUP lalu Enter).
     *
     * Sumber: App\Models\PermintaanFpupReferal (tabel permintaan_fpup_referal),
     * relasi details() -> PermintaanFpupReferalDetail.
     */
    public function cariFpup(string $noFpup): ?array
    {
        $fpup = \App\Models\PermintaanFpupReferal::query()
            ->with('details')
            ->where('no_fpup', $noFpup)
            ->first();

        if (! $fpup) {
            return null;
        }

        [$gol, $rhesus] = $this->parseGolRh($fpup->gol_rh_os);

        return [
            'fpup_id' => $fpup->id,
            'no_fpup' => $fpup->no_fpup,
            'tgl_fpup' => $this->formatTanggalJam($fpup->tgl_minta, $fpup->jam_minta),
            // TODO: tidak ada kolom yang jelas berpasangan dengan "NOFPUP Dari CM" di
            // permintaan_fpup_referal. Sementara dipetakan ke no_referal, ganti jika perlu.
            'nofpup_dari_cm' => $fpup->no_referal,
            // fpup_id pada tabel mereka = FK ke master pasien (tabel `fpup`), bukan id record ini.
            'pasien_id' => $fpup->fpup_id,
            'nama_pasien' => $fpup->nama_pasien,
            'noktp_pasien' => $fpup->no_ktp,
            'jenis_kelamin' => $this->normalizeJenisKelamin($fpup->jenis_kelamin),
            'alamat_pasien' => $fpup->alamat,
            'kode_rs' => $fpup->kode_rs,
            'nama_rs' => $fpup->nama_rs,
            'no_reg' => $fpup->no_reg,
            'gol_darah' => $gol,
            'rhesus' => $rhesus,
            'permintaan' => $fpup->details->map(fn ($d, $i) => [
                'no' => $i + 1,
                'jenis_darah' => $d->jns_darah,
                'gol' => $d->gol_darah,
                'rhesus' => $this->normalizeRhesus($d->rhesus),
                'jumlah' => $d->jumlah,
                'tgl_perlu' => optional($d->tgl_perlu)->format('d/m/Y'),
            ])->values(),
        ];
    }

    /**
     * Pecah string golongan darah gabungan (kolom gol_rh_os, contoh: "O Positif",
     * "O+", "AB-") menjadi [gol, rhesus].
     */
    private function parseGolRh(?string $value): array
    {
        $value = trim((string) $value);

        if ($value === '') {
            return ['', 'Positif'];
        }

        if (preg_match('/^(AB|A|B|O)\s*([+-]|Positif|Negatif)?/i', $value, $m)) {
            $gol = strtoupper($m[1]);
            $rhesusMentah = $m[2] ?? 'Positif';

            return [$gol, $this->normalizeRhesus($rhesusMentah)];
        }

        return [$value, 'Positif'];
    }

    private function normalizeRhesus(?string $value): string
    {
        $v = strtoupper(trim((string) $value));

        return in_array($v, ['NEGATIF', 'NEG', '-'], true) ? 'Negatif' : 'Positif';
    }

    private function normalizeJenisKelamin(?string $value): string
    {
        $v = strtolower(trim((string) $value));

        if (in_array($v, ['l', 'laki-laki', 'laki laki', 'pria', 'm', 'male'], true)) {
            return 'pria';
        }

        return 'wanita';
    }

    private function formatTanggalJam($tanggal, ?string $jam): ?string
    {
        if (! $tanggal) {
            return null;
        }

        $hasil = optional($tanggal)->format('d-m-Y');

        if ($jam) {
            try {
                $hasil .= ' '.\Illuminate\Support\Carbon::parse($jam)->format('H:i');
            } catch (\Throwable $e) {
                $hasil .= ' '.$jam;
            }
        }

        return $hasil;
    }

    /**
     * Cari stok darah yang sesuai golongan/rhesus/jenis untuk ditampilkan pada
     * tabel "Detail Pemeriksaan Awal".
     *
     * Sumber: App\Models\StokDarah (tabel stok_darah).
     * "Nostock" pada form dipetakan ke kolom no_kantong (fallback ke no_stok
     * kalau no_kantong kosong) — sesuaikan kalau kolom yang ingin ditampilkan beda.
     */
    public function searchStock(array $criteria): Collection
    {
        return \App\Models\StokDarah::query()
            ->tersedia()
            ->when($criteria['gol'] ?? null, fn ($q, $v) => $q->byGolongan($v))
            ->when($criteria['rhesus'] ?? null, fn ($q, $v) => $q->byRhesus($v))
            ->when($criteria['jns_darah'] ?? null, fn ($q, $v) => $q->byJenis($v))
            ->where('tgl_expired', '>=', now())
            ->orderBy('tgl_expired')
            ->get()
            ->map(fn ($s) => [
                'nostock' => $s->no_kantong ?: $s->no_stok,
                'jns_darah' => $s->jenis_darah,
                'gol' => $s->golongan_darah,
                'rhesus' => $s->rhesus,
                'tgl_aftap' => optional($s->tgl_aftap)->format('d-m-Y'),
                'tgl_produksi' => optional($s->tgl_produksi)->format('d-m-Y'),
                'tgl_kadaluarsa' => optional($s->tgl_expired)->format('d-m-Y'),
            ])
            ->values();
    }

    /**
     * Simpan pemberian awal referal baru. stocks & biaya_lain disimpan langsung
     * sebagai kolom JSON di tabel pemberian_awal_referal (tidak ada tabel anak).
     */
    public function store(array $data): PemberianAwalReferal
    {
        [$stocks, $biayaLain, $header] = $this->siapkanData($data);

        return PemberianAwalReferal::create([
            ...$header,
            'no_pemberian' => $this->generateNoPemberian(),
            'stocks' => $stocks,
            'biaya_lain' => $biayaLain,
            'jumlah_kantong_per_seleksi' => count($stocks),
            'total_biaya' => collect($biayaLain)->sum('subtotal'),
            'status' => $header['status'] ?? 'draft',
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);
    }

    public function update(PemberianAwalReferal $pemberian, array $data): PemberianAwalReferal
    {
        [$stocks, $biayaLain, $header] = $this->siapkanData($data);

        $pemberian->update([
            ...$header,
            'stocks' => $stocks,
            'biaya_lain' => $biayaLain,
            'jumlah_kantong_per_seleksi' => count($stocks),
            'total_biaya' => collect($biayaLain)->sum('subtotal'),
            'updated_by' => auth()->id(),
        ]);

        return $pemberian;
    }

    public function delete(PemberianAwalReferal $pemberian): void
    {
        $pemberian->delete();
    }

    /**
     * Cari master barang (untuk autocomplete kode/nama di "Rincian Biaya Lain").
     * Memilih hasil akan otomatis mengisi harga (harga_satuan) & satuan.
     *
     * Sumber: App\Models\Barang (tabel barang).
     */
    public function searchBarang(string $query): Collection
    {
        return \App\Models\Barang::query()
            ->where(function ($q) use ($query) {
                $q->where('kode', 'like', "%{$query}%")
                    ->orWhere('nama', 'like', "%{$query}%");
            })
            // TODO: kalau aplikasi ini multi-cabang dan barang per cabang berbeda,
            // tambahkan filter cabang_id sesuai cabang yang sedang login, contoh:
            // ->where('cabang_id', auth()->user()->cabang_id)
            ->orderBy('nama')
            ->limit(15)
            ->get(['id', 'kode', 'nama', 'satuan', 'harga_satuan'])
            ->map(fn ($b) => [
                'id' => $b->id,
                'kode' => $b->kode,
                'nama' => $b->nama,
                'satuan' => $b->satuan,
                'harga_satuan' => (float) $b->harga_satuan,
            ])
            ->values();
    }

    /**
     * Pisahkan input menjadi [stocks, biaya_lain, header] dan hitung subtotal
     * biaya + tandai setiap kantong yang dikirim sebagai "dipilih".
     */
    private function siapkanData(array $data): array
    {
        $stocks = collect($data['stocks'] ?? [])
            ->map(fn ($s) => [...$s, 'dipilih' => true])
            ->values()
            ->all();

        $biayaLain = collect($data['biaya_lain'] ?? [])
            ->map(fn ($b) => [...$b, 'subtotal' => $b['qty'] * $b['harga']])
            ->values()
            ->all();

        $header = collect($data)->except(['stocks', 'biaya_lain'])->toArray();

        return [$stocks, $biayaLain, $header];
    }
}