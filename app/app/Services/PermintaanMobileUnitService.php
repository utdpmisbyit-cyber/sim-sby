<?php

namespace App\Services;

use App\Models\PermintaanMobileUnit;
use App\Models\PermintaanMobileUnitDetail;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PermintaanMobileUnitService
{
    // ── Untuk controller lama (paginate + filter) ─────────────────────────────

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return PermintaanMobileUnit::with(['bagianPetugas', 'petugas', 'verifikator', 'details'])
            ->search($filters['search'] ?? null)
            ->when(isset($filters['flag']) && $filters['flag'] !== '', fn ($q) => $q->byFlag((int) $filters['flag']))
            ->when(isset($filters['tanggal_dari']), fn ($q) => $q->whereDate('tanggal', '>=', $filters['tanggal_dari']))
            ->when(isset($filters['tanggal_sampai']), fn ($q) => $q->whereDate('tanggal', '<=', $filters['tanggal_sampai']))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findOrFail(int $id): PermintaanMobileUnit
    {
        return PermintaanMobileUnit::with(['bagianPetugas', 'petugas', 'verifikator', 'details.tipeKantong'])
            ->findOrFail($id);
    }

    // ── Untuk controller AJAX (pola PermintaanKantong) ────────────────────────

    /**
     * Ambil semua data untuk ditampilkan di tabel riwayat (AJAX).
     */
    public function list(): array
    {
        return PermintaanMobileUnit::with(['bagianPetugas', 'petugas', 'details'])
            ->latest()
            ->get()
            ->map(fn ($p) => [
                'id'               => $p->id,
                'nomor'            => $p->nomor,
                'tanggal'          => $p->tanggal?->format('Y-m-d'),
                'bagian_petugas'   => $p->bagianPetugas?->nama,
                'bagian_petugas_id'=> $p->bagian_petugas_id,
                'petugas'          => $p->petugas?->nama,
                'petugas_id'       => $p->petugas_id,
                'verifikator_id'   => $p->verifikator_id,
                'flag'             => $p->flag,
                'keterangan'       => $p->keterangan,
                'details_count'    => $p->details->count(),
            ])
            ->toArray();
    }

    /**
     * Cari satu data lengkap dengan details (untuk form edit / show AJAX).
     */
    public function find(int $id): ?array
    {
        $p = PermintaanMobileUnit::with(['bagianPetugas', 'petugas', 'verifikator', 'details.tipeKantong'])
            ->find($id);

        if (!$p) return null;

        return [
            'id'               => $p->id,
            'nomor'            => $p->nomor,
            'tanggal'          => $p->tanggal?->format('Y-m-d'),
            'bagian_petugas'   => $p->bagianPetugas?->nama,
            'bagian_petugas_id'=> $p->bagian_petugas_id,
            'petugas'          => $p->petugas?->nama,
            'petugas_id'       => $p->petugas_id,
            'verifikator'      => $p->verifikator?->nama,
            'verifikator_id'   => $p->verifikator_id,
            'flag'             => $p->flag,
            'keterangan'       => $p->keterangan,
            'details'          => $p->details->map(fn ($d) => [
                'id'              => $d->id,
                'tipe_kantong_id' => $d->tipe_kantong_id,
                'tipe_kantong'    => $d->tipeKantong?->nama,
                'merk'            => $d->merk,
                'jenis'           => $d->jenis,
                'ukuran'          => $d->ukuran,
                'kode'            => $d->kode,
                'status'          => $d->status,
                'jumlah'          => $d->jumlah,
                'jumlah_dilayani' => $d->jumlah_dilayani,
            ])->toArray(),
        ];
    }

    // ── Mutasi ────────────────────────────────────────────────────────────────

    public function store(array $data): PermintaanMobileUnit
    {
        return DB::transaction(function () use ($data) {
            $permintaan = PermintaanMobileUnit::create([
                'nomor'             => $data['nomor'],
                'tanggal'           => $data['tanggal'],
                'bagian_petugas_id' => $data['bagian_petugas_id'] ?? null,
                'petugas_id'        => $data['petugas_id']        ?? null,
                'verifikator_id'    => $data['verifikator_id']    ?? null,
                'keterangan'        => $data['keterangan']        ?? null,
                'flag'              => $data['flag']              ?? 0,
            ]);

            $this->syncDetails($permintaan, $data['details'] ?? []);

            return $permintaan->load(['bagianPetugas', 'petugas', 'details']);
        });
    }

    /**
     * Update by ID (pola AJAX — menerima int $id bukan model).
     * Kembalikan false jika tidak ditemukan.
     */
    public function update(int $id, array $data): bool
    {
        $permintaan = PermintaanMobileUnit::find($id);

        if (!$permintaan) return false;

        DB::transaction(function () use ($permintaan, $data) {
            $permintaan->update([
                'nomor'             => $data['nomor']             ?? $permintaan->nomor,
                'tanggal'           => $data['tanggal']           ?? $permintaan->tanggal,
                'bagian_petugas_id' => $data['bagian_petugas_id'] ?? null,
                'petugas_id'        => $data['petugas_id']        ?? null,
                'verifikator_id'    => $data['verifikator_id']    ?? null,
                'keterangan'        => $data['keterangan']        ?? null,
                'flag'              => $data['flag']              ?? $permintaan->flag,
            ]);

            $this->syncDetails($permintaan, $data['details'] ?? []);
        });

        return true;
    }

    /**
     * Delete by ID (pola AJAX).
     */
    public function delete(int $id): void
    {
        $permintaan = PermintaanMobileUnit::find($id);

        if (!$permintaan) return;

        DB::transaction(function () use ($permintaan) {
            $permintaan->details()->delete();
            $permintaan->delete();
        });
    }

    /**
     * Delete by model (dipakai controller lama jika masih ada).
     */
    public function destroy(PermintaanMobileUnit $permintaan): void
    {
        DB::transaction(function () use ($permintaan) {
            $permintaan->details()->delete();
            $permintaan->delete();
        });
    }

    public function updateFlag(PermintaanMobileUnit $permintaan, int $flag): void
    {
        $permintaan->update(['flag' => $flag]);
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function syncDetails(PermintaanMobileUnit $permintaan, array $details): void
    {
        $keepIds = collect($details)->pluck('id')->filter()->all();
        $permintaan->details()->whereNotIn('id', $keepIds)->delete();

        foreach ($details as $detail) {
            $permintaan->details()->updateOrCreate(
                ['id' => $detail['id'] ?? null],
                [
                    'tipe_kantong_id' => $detail['tipe_kantong_id'] ?? null,
                    'jumlah'          => $detail['jumlah']          ?? 1,
                    'jumlah_dilayani' => $detail['jumlah_dilayani'] ?? 0,
                    'kode'            => $detail['kode']            ?? null,
                    'merk'            => $detail['merk']            ?? null,
                    'jenis'           => $detail['jenis']           ?? null,
                    'ukuran'          => $detail['ukuran']          ?? null,
                    'status'          => $detail['status']          ?? null,
                    'flag'            => $detail['flag']            ?? 0,
                ]
            );
        }
    }

    public function generateNomor(): string
    {
        $prefix = 'PMU';
        $year   = now()->format('Y');
        $month  = now()->format('m');

        $lastNomor = PermintaanMobileUnit::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->orderByDesc('id')
            ->value('nomor');

        $seq = 1;
        if ($lastNomor) {
            $parts = explode('/', $lastNomor);
            $seq   = ((int) end($parts)) + 1;
        }

        return sprintf('%s/%s/%s/%04d', $prefix, $year, $month, $seq);
    }
}