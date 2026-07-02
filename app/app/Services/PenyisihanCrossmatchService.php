<?php

namespace App\Services;

use App\Models\CrossTest;
use App\Models\PenyisihanCrossmatch;
use App\Models\PenyisihanCrossmatchDetail;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PenyisihanCrossmatchService
{
    /**
     * Daftar alasan penyisihan — sudah jadi 1 kolom enum langsung di
     * table penyisihan_crossmatch_details (tidak pakai table master lagi).
     */
    public const ALASAN_OPTIONS = [
        'Selang Pendek',
        'Bocor',
        'Pasien Meninggal',
        'Keruh',
        'Expired Date',
        'Lab Luar Reaktif',
        'Darah Tidak Terserap',
        'DCT Positif / Mayor Positif',
    ];

    public function alasanOptions(): array
    {
        return self::ALASAN_OPTIONS;
    }

    /**
     * List header penyisihan untuk halaman index, lengkap dengan pencarian & filter tanggal.
     */
    public function paginateList(?string $search, ?string $tanggalDari, ?string $tanggalSampai, int $perPage = 10): LengthAwarePaginator
    {
        return PenyisihanCrossmatch::query()
            ->search($search)
            ->when($tanggalDari, fn ($q) => $q->whereDate('tanggal_penyisihan', '>=', $tanggalDari))
            ->when($tanggalSampai, fn ($q) => $q->whereDate('tanggal_penyisihan', '<=', $tanggalSampai))
            ->withCount('details')
            ->orderByDesc('tanggal_penyisihan')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Generate nomor penyisihan otomatis, format: D + YYMMDD + nomor urut 4 digit.
     * Contoh: D2606210001
     */
    public function generateNoPenyisihan(): string
    {
        $prefix = 'D' . now()->format('ymd');

        $last = PenyisihanCrossmatch::withTrashed()
            ->where('no_penyisihan', 'like', $prefix . '%')
            ->orderByDesc('no_penyisihan')
            ->first();

        $sequence = 1;

        if ($last) {
            $lastSequence = (int) substr($last->no_penyisihan, strlen($prefix));
            $sequence = $lastSequence + 1;
        }

        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Dipanggil saat user scan / input No Stock pada form.
     * Data kantong darah diambil dari table cross_tests sesuai permintaan.
     */
    public function findStockForScan(string $noStock, ?int $excludeDetailId = null): array
    {
        $crossTest = CrossTest::where('no_stock', $noStock)->first();

        if (! $crossTest) {
            throw ValidationException::withMessages([
                'no_stock' => 'Nomor stock tidak ditemukan pada data cross test.',
            ]);
        }

        $sudahDisisihkan = PenyisihanCrossmatchDetail::where('no_stock', $noStock)
            ->when($excludeDetailId, fn ($q) => $q->where('id', '!=', $excludeDetailId))
            ->exists();

        if ($sudahDisisihkan) {
            throw ValidationException::withMessages([
                'no_stock' => 'Kantong darah dengan nomor stock ini sudah pernah disisihkan.',
            ]);
        }

        return [
            'cross_test_id'  => $crossTest->id,
            'no_stock'       => $crossTest->no_stock,
            'jns_darah'      => $crossTest->jns_darah,
            'gol_rh_kantong' => $crossTest->gol_rh_kantong,
            'gol'            => $crossTest->gol,
            'rhesus'         => $crossTest->rhesus,
            'tgl_aftap'      => optional($crossTest->tgl_ambil)->format('Y-m-d'),
            'tgl_kadaluarsa' => optional($crossTest->tgl_kadaluarsa)->format('Y-m-d'),
            'status_kantong' => $crossTest->status,
        ];
    }

    /**
     * Simpan header + seluruh detail kantong darah dalam satu transaksi DB.
     */
    public function store(array $data): PenyisihanCrossmatch
    {
        return DB::transaction(function () use ($data) {
            $header = PenyisihanCrossmatch::create([
                'no_penyisihan'      => $data['no_penyisihan'] ?? $this->generateNoPenyisihan(),
                'tanggal_penyisihan' => $data['tanggal_penyisihan'],
                'petugas'            => $data['petugas'] ?? null,
                'status'             => 'selesai',
                'keterangan'         => $data['keterangan'] ?? null,
                'jumlah'             => count($data['items']),
            ]);

            foreach ($data['items'] as $item) {
                $header->details()->create($this->mapDetailPayload($item));
            }

            return $header->load('details');
        });
    }

    /**
     * Update header + replace seluruh detail (item lama dihapus, diganti dengan
     * daftar item terbaru dari form — sesuai pola scan-tambah-simpan yang sama).
     */
    public function update(PenyisihanCrossmatch $header, array $data): PenyisihanCrossmatch
    {
        return DB::transaction(function () use ($header, $data) {
            $header->update([
                'tanggal_penyisihan' => $data['tanggal_penyisihan'],
                'petugas'            => $data['petugas'] ?? null,
                'keterangan'         => $data['keterangan'] ?? null,
                'jumlah'             => count($data['items']),
            ]);

            $header->details()->delete();

            foreach ($data['items'] as $item) {
                $header->details()->create($this->mapDetailPayload($item));
            }

            return $header->load('details');
        });
    }

    public function delete(PenyisihanCrossmatch $header): void
    {
        DB::transaction(function () use ($header) {
            $header->details()->delete();
            $header->delete();
        });
    }

    private function mapDetailPayload(array $item): array
    {
        return [
            'cross_test_id'  => $item['cross_test_id'] ?? null,
            'no_stock'       => $item['no_stock'],
            'jns_darah'      => $item['jns_darah'] ?? null,
            'gol_rh_kantong' => $item['gol_rh_kantong'] ?? null,
            'gol'            => $item['gol'] ?? null,
            'rhesus'         => $item['rhesus'] ?? null,
            'tgl_aftap'      => $item['tgl_aftap'] ?? null,
            'tgl_kadaluarsa' => $item['tgl_kadaluarsa'] ?? null,
            'status_kantong' => $item['status_kantong'] ?? null,
            'alasan'         => $item['alasan'],
            'keterangan'     => $item['keterangan'] ?? null,
        ];
    }
}