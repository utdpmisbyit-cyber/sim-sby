<?php

namespace App\Services;

use App\Models\JenisBiaya;
use App\Models\PengembalianBiayaCrosstest;
use App\Models\PermintaanFpup;
use App\Models\Petugas;
use App\Models\ServiceCost;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

class PengembalianBiayaCrosstestService
{
    /**
     * Generate nomor retur baru, format: RT + YYMM + nomor urut 4 digit.
     * Contoh: RT26060001
     */
    public function generateNoRetur(): string
    {
        $prefix = 'RT' . now()->format('ym');

        // withTrashed() PENTING: kalau tidak, record yang sudah soft-delete (deleted_at terisi)
        // tidak ikut dihitung, padahal nilai no_retur-nya masih ada di kolom unique di DB.
        // Tanpa ini, sistem bisa terus-menerus mengusulkan nomor yang sebenarnya sudah dipakai
        // (oleh record yang sudah dihapus), dan insert akan gagal terus dengan error duplicate.
        $last = PengembalianBiayaCrosstest::withTrashed()
            ->where('no_retur', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->first();

        $next = $last ? ((int) substr($last->no_retur, -4)) + 1 : 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Ambil data FPUP berdasarkan no_fpup, lengkap dengan item detail darah
     * dan saran biaya per item (diambil dari service_cost x jumlah).
     */
    public function scanFpup(string $noFpup): ?array
    {
        $fpup = PermintaanFpup::with('details')
            ->where('no_fpup', $noFpup)
            ->first();

        if (! $fpup) {
            return null;
        }

        // Jenis biaya diambil OTOMATIS dari kolom jns_biaya di permintaan_fpup
        // (contoh nilai: "NATBPJS", "NATBPPD", dll). Kolom cara_pembayaran ("TUNAI", "BPJS", dst)
        // adalah metode bayar, bukan kategori biaya, jadi TIDAK dipakai sebagai fallback di sini.
        $jenisBiayaTeks = $fpup->jns_biaya;

        $candidates  = $this->findServiceCostCandidates($fpup->jenis_rs, $jenisBiayaTeks);
        $serviceCost = $candidates->first();

        // Jumlah ("stok" yang diminta) diambil langsung dari permintaan_fpup_detail.jumlah,
        // dikalikan harga_satuan dari service_cost yang cocok -> ini dasar hitung otomatis.
        $items = $fpup->details->map(function ($detail) use ($serviceCost) {
            $harga  = $serviceCost?->biaya ?? 0;
            $jumlah = (int) ($detail->jumlah ?: 1);

            return [
                'permintaan_fpup_detail_id' => $detail->id,
                'nama_os'      => null, // diisi dari header di sisi front-end
                'jns_darah'    => $detail->jns_darah,
                'gol_darah'    => $detail->gol_darah,
                'rhesus'       => $detail->rhesus,
                'jumlah'       => $jumlah,
                'cc'           => $detail->cc,
                'harga_satuan' => (float) $harga,
                'subtotal'     => (float) $harga * $jumlah,
            ];
        })->values();

        return [
            'fpup' => [
                'id'          => $fpup->id,
                'no_fpup'     => $fpup->no_fpup,
                'tgl_fpup'    => optional($fpup->tgl_minta)->format('Y-m-d'),
                'no_reg'      => $fpup->no_reg,
                'kode_rs'     => $fpup->kode_rs,
                'nama_rs'     => $fpup->nama_rs,
                'jenis_rs'    => $fpup->jenis_rs,
                'kategori_rs' => $fpup->kategori_rs,
                'bagian'      => $fpup->bagian,
                'kelas_rawat' => $fpup->kelas_rawat,
                'nama_pasien' => $fpup->nama_pasien,
                'no_ktp'      => $fpup->no_ktp,
                'nama_dokter' => $fpup->nama_dokter,
                'jns_biaya'   => $fpup->jns_biaya,
            ],
            'jenis_biaya_candidates' => $candidates->map(fn ($c) => [
                'id'             => $c->id,
                'kode'           => $c->kode,
                'nama'           => $c->nama,
                'biaya'          => (float) $c->biaya,
                'jenis_biaya_id' => $c->jenis_biaya_id,
            ])->values(),
            'service_cost_dipakai' => $serviceCost ? [
                'id'    => $serviceCost->id,
                'kode'  => $serviceCost->kode,
                'nama'  => $serviceCost->nama,
                'biaya' => (float) $serviceCost->biaya,
            ] : null,
            'items'       => $items->map(fn ($i) => $i + ['nama_os' => $fpup->nama_pasien])->values(),
            'total_saran' => (float) $items->sum('subtotal'),
        ];
    }

    /**
     * Cari kandidat service_cost yang cocok dengan teks jenis biaya dari FPUP (jns_biaya),
     * lalu disaring lagi dengan jenis RS (Pemerintah/Swasta) kalau memang ada datanya.
     *
     * Catatan penting: kolom `service_cost.kode` cuma nomor urut (001, 002, ...),
     * BUKAN kode jenis biaya. Yang berisi teks bermakna (mis. "PKNATBPPD", "PKOMASKES")
     * adalah kolom `service_cost.nama`, dan relasi resminya ke jenis biaya adalah lewat
     * `jenis_biaya_id` (ServiceCost belongsTo JenisBiaya).
     *
     * PRIORITAS PALING PENTING: begitu jenis_biaya_id sudah ketemu cocok dengan jns_biaya FPUP,
     * itu wajib dipakai — walau ternyata tidak ada baris untuk jenis RS yang sama persis.
     * Sistem TIDAK BOLEH lompat mengambil baris lain yang jenis_biaya_id-nya beda hanya karena
     * filter jenis RS gagal; itu akan menghasilkan harga yang sama sekali tidak berkaitan.
     */
    public function findServiceCostCandidates(?string $jenisRs, ?string $jenisBiayaTeks): Collection
    {
        $jenisBiaya = null;

        if ($jenisBiayaTeks) {
            // Prioritas: (a) exact match ke nama, (b) exact match ke kode, (c) fuzzy LIKE ke nama.
            $jenisBiaya = JenisBiaya::where('nama', $jenisBiayaTeks)->first()
                ?? JenisBiaya::where('kode', $jenisBiayaTeks)->first()
                ?? JenisBiaya::where('nama', 'like', "%{$jenisBiayaTeks}%")->first();
        }

        // (1) jenis_biaya_id ketemu, DAN ada service_cost yang jenis RS-nya juga cocok -> ideal
        if ($jenisBiaya) {
            $result = ServiceCost::with('jenisBiaya')
                ->where('jenis_biaya_id', $jenisBiaya->id)
                ->when($jenisRs, fn ($q) => $q->where('jenis', $jenisRs))
                ->orderBy('nama')->get();

            if ($result->isNotEmpty()) {
                return $result;
            }

            // (2) jenis_biaya_id ketemu, tapi TIDAK ADA versi untuk jenis RS tersebut
            // (mis. "NATBPJS" cuma terdaftar untuk Pemerintah, RS pasien Swasta).
            // Tetap pakai jenis_biaya_id ini — JANGAN lompat ke baris lain yang tidak
            // berkaitan sama sekali hanya karena kebetulan satu-satunya opsi untuk jenis RS itu.
            $result = ServiceCost::with('jenisBiaya')
                ->where('jenis_biaya_id', $jenisBiaya->id)
                ->orderBy('nama')->get();

            if ($result->isNotEmpty()) {
                return $result;
            }
        }

        // (3) jenis_biaya tidak ketemu sama sekali -> coba cocokkan teks langsung ke nama service_cost
        if ($jenisBiayaTeks) {
            $result = ServiceCost::with('jenisBiaya')
                ->where('nama', 'like', '%' . $jenisBiayaTeks . '%')
                ->when($jenisRs, fn ($q) => $q->where('jenis', $jenisRs))
                ->orderBy('nama')->get();

            if ($result->isNotEmpty()) {
                return $result;
            }
        }

        // (4) benar-benar tidak ada yang cocok -> tampilkan semua opsi untuk jenis RS terkait,
        // supaya kasir tetap bisa pilih manual (bukan dibiarkan kosong).
        if ($jenisRs) {
            return ServiceCost::with('jenisBiaya')->where('jenis', $jenisRs)->orderBy('nama')->get();
        }

        return ServiceCost::with('jenisBiaya')->orderBy('nama')->get();
    }

    /**
     * Daftar opsi jenis biaya / service_cost untuk dropdown (dipakai endpoint api/jenis-biaya).
     */
    public function jenisBiayaOptions(?string $jenisRs = null): SupportCollection
    {
        $query = ServiceCost::query()->with('jenisBiaya');

        if ($jenisRs) {
            $query->where('jenis', $jenisRs);
        }

        return $query->orderBy('nama')->get()->map(fn ($c) => [
            'id'          => $c->id,
            'kode'        => $c->kode,
            'nama'        => $c->nama,
            'biaya'       => (float) $c->biaya,
            'jenis_biaya' => $c->jenisBiaya?->nama,
        ]);
    }

    public function hargaSatuan(int $serviceCostId): float
    {
        return (float) (ServiceCost::find($serviceCostId)?->biaya ?? 0);
    }

    /**
     * Cari petugas (kasir) berdasarkan nama atau kode, dipakai untuk autocomplete
     * field "Kode Kasir" di form retur.
     */
    public function cariKasir(string $keyword, int $limit = 10): SupportCollection
    {
        return Petugas::query()
            ->where(function ($w) use ($keyword) {
                $w->where('nama', 'like', "%{$keyword}%")
                  ->orWhere('kode', 'like', "%{$keyword}%");
            })
            ->orderBy('nama')
            ->limit($limit)
            ->get(['id', 'kode', 'nama'])
            ->map(fn ($p) => [
                'id'   => $p->id,
                'kode' => $p->kode,
                'nama' => $p->nama,
            ]);
    }

    /**
     * Simpan transaksi retur + detail item dalam satu transaksi DB.
     *
     * Anti-duplikat no_retur: kalau insert gagal karena nomor sudah dipakai
     * (race condition antar request, double-submit, atau ada data lama yang
     * bikin perhitungan "nomor terakhir" jadi tidak akurat), sistem TIDAK
     * generate ulang dari query yang sama (yang bisa saja mengembalikan nomor
     * yang sama lagi) — melainkan langsung menaikkan angka dari nomor yang
     * baru gagal dipakai, lalu coba insert lagi. Ini dijamin selalu maju
     * (progress), jadi tidak akan mentok di nomor yang sama berulang-ulang.
     */
    public function store(array $data): PengembalianBiayaCrosstest
    {
        $maxAttempt = 10;
        $noRetur    = $this->generateNoRetur();

        for ($attempt = 1; $attempt <= $maxAttempt; $attempt++) {
            try {
                return DB::transaction(function () use ($data, $noRetur) {
                    $fpup = PermintaanFpup::where('no_fpup', $data['no_fpup'])->first();

                    $header = PengembalianBiayaCrosstest::create([
                        'permintaan_fpup_id' => $fpup?->id,
                        'no_retur'           => $noRetur,
                        'no_fpup'            => $data['no_fpup'],
                        'tgl_fpup'           => $fpup?->tgl_minta,
                        'no_reg'             => $fpup?->no_reg,
                        'kode_rs'            => $fpup?->kode_rs,
                        'nama_rs'            => $fpup?->nama_rs,
                        'jenis_rs'           => $fpup?->jenis_rs,
                        'kategori_rs'        => $fpup?->kategori_rs,
                        'bagian'             => $fpup?->bagian,
                        'kelas_rawat'        => $fpup?->kelas_rawat,
                        'nama_pasien'        => $fpup?->nama_pasien,
                        'no_ktp'             => $fpup?->no_ktp,
                        'nama_dokter'        => $fpup?->nama_dokter,
                        'jns_biaya'          => $fpup?->jns_biaya,
                        'jenis_biaya_id'     => $data['jenis_biaya_id'] ?? null,
                        'kode_service_cost'  => $data['kode_service_cost'] ?? null,
                        'sub_total'          => collect($data['items'])->sum(fn ($i) => $i['harga_satuan'] * $i['jumlah']),
                        'total_retur'        => $data['total_retur'],
                        'tgl_retur'          => now(),
                        'kode_kasir'         => $data['kode_kasir'] ?? null,
                        'nama_kasir'         => $data['nama_kasir'] ?? null,
                        'no_nota'            => $data['no_nota'] ?? null,
                        'keterangan'         => $data['keterangan'] ?? null,
                        'status'             => 'disimpan',
                        'created_by'         => auth()->id(),
                    ]);

                    foreach ($data['items'] as $item) {
                        $header->details()->create([
                            'permintaan_fpup_detail_id' => $item['permintaan_fpup_detail_id'] ?? null,
                            'nama_os'           => $item['nama_os'] ?? $fpup?->nama_pasien,
                            'no_minta'          => $data['no_fpup'],
                            'kode_rs'           => $fpup?->kode_rs,
                            'nama_rs'           => $fpup?->nama_rs,
                            'jenis_rs'          => $fpup?->jenis_rs,
                            'bagian_rawat'      => $fpup?->bagian,
                            'jns_darah'         => $item['jns_darah'] ?? null,
                            'gol_darah'         => $item['gol_darah'] ?? null,
                            'rhesus'            => $item['rhesus'] ?? null,
                            'jumlah'            => $item['jumlah'],
                            'cc'                => $item['cc'] ?? null,
                            'kode_service_cost' => $data['kode_service_cost'] ?? null,
                            'harga_satuan'      => $item['harga_satuan'],
                            'subtotal'          => $item['harga_satuan'] * $item['jumlah'],
                        ]);
                    }

                    return $header;
                });
            } catch (\Illuminate\Database\QueryException $e) {
                $isDuplicateNoRetur = $e->getCode() === '23000'
                    && str_contains($e->getMessage(), 'no_retur');

                // Kalau bukan soal duplicate no_retur, atau sudah kehabisan jatah coba ulang,
                // lempar error aslinya supaya tidak menyembunyikan masalah lain.
                if (! $isDuplicateNoRetur || $attempt === $maxAttempt) {
                    throw $e;
                }

                // Langsung naikkan angka dari nomor yang baru gagal dipakai (bukan generate
                // ulang dari query "nomor terakhir" yang bisa saja mengulang nilai yang sama).
                $noRetur = $this->bumpNoRetur($noRetur);
                usleep(30_000);
            }
        }

        // Tidak akan tercapai secara normal, hanya jaga-jaga untuk static analysis.
        throw new \RuntimeException('Gagal menyimpan retur setelah beberapa kali percobaan.');
    }

    /**
     * Naikkan 1 angka dari nomor retur yang diberikan, format tetap sama.
     * Contoh: RT26060001 -> RT26060002
     */
    protected function bumpNoRetur(string $noRetur): string
    {
        $prefix = substr($noRetur, 0, -4);
        $number = (int) substr($noRetur, -4) + 1;

        return $prefix . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Update header + ganti seluruh detail item dalam satu transaksi.
     * Detail lama dihapus lalu dibuat ulang — lebih sederhana & aman daripada
     * mencocokkan satu-satu mana yang berubah/dihapus/ditambah.
     */
    public function update(PengembalianBiayaCrosstest $header, array $data): PengembalianBiayaCrosstest
    {
        return DB::transaction(function () use ($header, $data) {
            $header->update([
                'kode_kasir'        => $data['kode_kasir'] ?? null,
                'nama_kasir'        => $data['nama_kasir'] ?? null,
                'no_nota'           => $data['no_nota'] ?? null,
                'keterangan'        => $data['keterangan'] ?? null,
                'jenis_biaya_id'    => $data['jenis_biaya_id'] ?? $header->jenis_biaya_id,
                'kode_service_cost' => $data['kode_service_cost'] ?? $header->kode_service_cost,
                'sub_total'         => collect($data['items'])->sum(fn ($i) => $i['harga_satuan'] * $i['jumlah']),
                'total_retur'       => $data['total_retur'],
                'updated_by'        => auth()->id(),
            ]);

            $header->details()->delete();

            foreach ($data['items'] as $item) {
                $header->details()->create([
                    'permintaan_fpup_detail_id' => $item['permintaan_fpup_detail_id'] ?? null,
                    'nama_os'           => $item['nama_os'] ?? $header->nama_pasien,
                    'no_minta'          => $header->no_fpup,
                    'kode_rs'           => $header->kode_rs,
                    'nama_rs'           => $header->nama_rs,
                    'jenis_rs'          => $header->jenis_rs,
                    'bagian_rawat'      => $header->bagian,
                    'jns_darah'         => $item['jns_darah'] ?? null,
                    'gol_darah'         => $item['gol_darah'] ?? null,
                    'rhesus'            => $item['rhesus'] ?? null,
                    'jumlah'            => $item['jumlah'],
                    'cc'                => $item['cc'] ?? null,
                    'kode_service_cost' => $data['kode_service_cost'] ?? $header->kode_service_cost,
                    'harga_satuan'      => $item['harga_satuan'],
                    'subtotal'          => $item['harga_satuan'] * $item['jumlah'],
                ]);
            }

            return $header->fresh('details');
        });
    }
}