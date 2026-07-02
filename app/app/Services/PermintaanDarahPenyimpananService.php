<?php

namespace App\Services;

use App\Models\PermintaanDarahPenyimpanan;
use App\Models\PermintaanDarahPenyimpananDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\BankDarah;

class PermintaanDarahPenyimpananService
{
    /**
     * Daftar permintaan dengan pagination + filter.
     * FIX: with('details') dan with('user') agar data tersedia di blade.
     */
    public function index(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return PermintaanDarahPenyimpanan::with(['details', 'user'])
            ->filter($filters)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Ambil satu permintaan beserta detailnya.
     * FIX: load('details') konsisten dengan nama relasi.
     */
    public function find(int $id): PermintaanDarahPenyimpanan
    {
        return PermintaanDarahPenyimpanan::with(['details', 'user'])->findOrFail($id);
    }

    /**
     * Buat permintaan baru beserta detail (dalam satu transaksi).
     */
     public function store(array $data): PermintaanDarahPenyimpanan
    {
        return DB::transaction(function () use ($data) {

            $permintaan = PermintaanDarahPenyimpanan::create([
                'no_permintaan'   => PermintaanDarahPenyimpanan::generateNomor(),

                'bank_darah_kode' => $data['bank_darah_kode'],
                'bank_darah_nama' => $data['bank_darah_nama'],

                'petugas_kode'    => $data['petugas_kode'] ?? null,
                'petugas_nama'    => $data['petugas_nama'] ?? null,

                'tanggal_minta'   => $data['tanggal_minta'],
                'status'          => PermintaanDarahPenyimpanan::STATUS_PERMINTAAN,
                'keterangan'      => $data['keterangan'] ?? null,
                'created_by'      => auth()->id(),
            ]);

            foreach ($data['detail'] as $item) {

                $permintaan->details()->create([
                    'jenis_darah'    => $item['jenis_darah'],
                    'golongan_darah' => $item['golongan_darah'],
                    'rhesus'         => $item['rhesus'],
                    'jumlah_kantong' => $item['jumlah_kantong'],
                    'jumlah_cc'      => $item['jumlah_cc'] ?? 0,
                    'tanggal_perlu'  => $item['tanggal_perlu'] ?? null,
                    'no_fpup'        => $item['no_fpup'] ?? null,
                    'nama_os'        => $item['nama_os'] ?? null,
                    'status'         => $item['status'] ?? 'permintaan',
                    'keterangan'     => $item['keterangan'] ?? null,
                ]);
            }

            return $permintaan->load('details');
        });
    }
    private function generateUniqueNomor(): string
    {
        do {
            $nomor = PermintaanDarahPenyimpanan::generateNomor();
        } while (
            PermintaanDarahPenyimpanan::where('no_permintaan', $nomor)->exists()
        );

        return $nomor;
    }
    /**
     * Update permintaan dan detail.
     */
     public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $header = PermintaanDarahPenyimpanan::findOrFail($id);
            $header->update([
                'bank_darah_kode' => $data['bank_darah_kode'],
                'bank_darah_nama' => $data['bank_darah_nama'],
                'tanggal_minta'   => $data['tanggal_minta'],
                'updated_by'      => auth()->id(),

            ]);

            $header->details()->delete();

            foreach ($data['detail'] as $detail) {
                $header->details()->create([
                    'jenis_darah'    => $detail['jenis_darah'] ?? null,
                    'golongan_darah' => $detail['golongan_darah'] ?? null,
                    'rhesus'         => $detail['rhesus'] ?? 'Positif',
                    'jumlah_kantong' => $detail['jumlah_kantong'] ?? 1,
                    'jumlah_cc'      => $detail['jumlah_cc'] ?? 0,
                    'tanggal_perlu'  => $detail['tanggal_perlu'] ?? null,
                    'no_fpup'        => $detail['no_fpup'] ?? null,
                    'status'         => 'permintaan',

                ]);
            }

            return $header->load('details');
        });
    }

    /**
     * Update status permintaan.
     */
    public function updateStatus(int $id, string $status): PermintaanDarahPenyimpanan
    {
        $permintaan = PermintaanDarahPenyimpanan::findOrFail($id);
        $permintaan->update(['status' => $status, 'updated_by' => Auth::id()]);
        return $permintaan;
    }

    /**
     * Hapus permintaan (soft delete).
     */
    public function destroy(int $id): void
    {
        $permintaan = PermintaanDarahPenyimpanan::findOrFail($id);
        $permintaan->delete();
    }

    /**
     * Sinkronisasi baris detail: hapus yang lama, masukkan yang baru.
     */
    private function syncDetail(PermintaanDarahPenyimpanan $permintaan, array $rows): void
    {
        $permintaan->details()->delete();

        if (empty($rows)) {
            return;
        }

        $insert = array_map(fn($r) => [
            'permintaan_darah_id' => $permintaan->id,
            'jenis_darah'         => $r['jenis_darah'],
            'golongan_darah'      => $r['golongan_darah'],
            'rhesus'              => $r['rhesus']          ?? 'Positif',
            'jumlah_kantong'      => (int) ($r['jumlah_kantong'] ?? 1),
            'jumlah_cc'           => (int) ($r['jumlah_cc']      ?? 0),
            'tanggal_perlu'       => $r['tanggal_perlu']  ?? null,
            'no_fpup'             => $r['no_fpup']         ?? null,
            'nama_os'             => $r['nama_os']         ?? null,
            'status'              => $r['status']          ?? 'permintaan',
            'keterangan'          => $r['keterangan']      ?? null,
            'created_at'          => now(),
            'updated_at'          => now(),
        ], $rows);

        PermintaanDarahPenyimpananDetail::insert($insert);
    }
}