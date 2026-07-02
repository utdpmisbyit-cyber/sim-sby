<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\StokDarah;
use App\Models\TransaksiStokDarah;
use App\Models\PengembalianDarahExternal;
use App\Models\PengembalianDarahExternalDetail;

class PengembalianDarahExternalService
{
    
    public function generateNomor(): string
    {
        $prefix = 'PKE';
        $today  = now()->format('Ymd');
        $last   = PengembalianDarahExternal::withTrashed()
            ->where('no_pengembalian', 'like', "{$prefix}{$today}%")
            ->orderByDesc('id')
            ->value('no_pengembalian');

        if ($last) {
            $seq = (int) substr($last, -4) + 1;
        } else {
            $seq = 1;
        }

        return $prefix . $today . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    // ─── Store ────────────────────────────────────────────────────────────────

    public function store(array $data): PengembalianDarahExternal
    {
        return DB::transaction(function () use ($data) {
            $header = PengembalianDarahExternal::create([
                'no_pengembalian'   => $this->generateNomor(),
                'tgl_pengembalian'  => $data['tgl_pengembalian'],
                'tujuan_darah'      => $data['tujuan_darah']      ?? null,
                'petugas_terima'     => $data['petugas_terima'] ?? null,
                'petugas_kembali'    => $data['petugas_kembali']?? null,
                'keterangan'        => $data['keterangan']        ?? null,
                'status'            => 'selesai',
                'created_by'        => Auth::id(),
                'updated_by'        => Auth::id(),
            ]);

            foreach ($data['details'] as $row) {
                $stok = StokDarah::where('no_stok', $row['no_stok'])->first();

                $detail = PengembalianDarahExternalDetail::create([
                    'pengembalian_id' => $header->id,
                    'no_stok'         => $row['no_stok'],
                    'no_kantong'      => $stok?->no_kantong,
                    'stok_darah_id'   => $stok?->id,
                    'jenis_darah'     => $stok?->jenis_darah   ?? $row['jenis_darah']   ?? null,
                    'golongan_darah'  => $stok?->golongan_darah?? $row['golongan_darah']?? null,
                    'rhesus'          => $stok?->rhesus         ?? $row['rhesus']         ?? null,
                    'tgl_aftap'       => $stok?->tgl_aftap      ?? $row['tgl_aftap']      ?? null,
                    'tgl_expired'     => $stok?->tgl_expired    ?? $row['tgl_expired']    ?? null,
                    'status_stok'     => $stok?->status_stok    ?? null,
                    'status_kembali'  => $row['status_kembali'] ?? null,
                    'alasan_kembali'  => $row['alasan_kembali'] ?? null,
                    'jumlah'          => $row['jumlah']         ?? 1,
                    'keterangan'      => $row['keterangan']     ?? null,
                ]);

                // Update stok_darah
                if ($stok) {
                    $stok->increment('jumlah_kembali');
                    $stok->increment('saldo');
                    $stok->update(['status_stok' => 'tersedia']);
                }

                // Catat transaksi
                TransaksiStokDarah::create([
                    'no_stok'        => $row['no_stok'],
                    'stok_darah_id'  => $stok?->id,
                    'jenis'          => 'kembali',
                    'jumlah'         => $detail->jumlah,
                    'no_referensi'   => $header->no_pengembalian,
                    'sumber'         => 'pengembalian_external',
                    'referensi_id'   => $header->id,
                    'keterangan'     => $detail->alasan_kembali,
                    'petugas_id'     => $data['petugas_kembali_id'] ?? null,
                    'created_by'     => Auth::id(),
                ]);
            }

            return $header->load('details');
        });
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    public function update(PengembalianDarahExternal $pengembalian, array $data): PengembalianDarahExternal
    {
        return DB::transaction(function () use ($pengembalian, $data) {
            // Rollback transaksi lama
            foreach ($pengembalian->details as $oldDetail) {
                $stok = StokDarah::where('no_stok', $oldDetail->no_stok)->first();
                if ($stok) {
                    $stok->decrement('jumlah_kembali');
                    $stok->decrement('saldo');
                }
                TransaksiStokDarah::where('referensi_id', $pengembalian->id)
                    ->where('sumber', 'pengembalian_external')
                    ->where('no_stok', $oldDetail->no_stok)
                    ->delete();
            }
            $pengembalian->details()->delete();

            // Update header
            $pengembalian->update([
                'tgl_pengembalian'   => $data['tgl_pengembalian'],
                'tujuan_darah'       => $data['tujuan_darah']      ?? null,
                'petugas_terima'  => $data['petugas_terima'] ?? null,
                'petugas_kembali' => $data['petugas_kembali']?? null,
                'keterangan'         => $data['keterangan']        ?? null,
                'updated_by'         => Auth::id(),
            ]);

            // Re-insert detail
            foreach ($data['details'] as $row) {
                $stok = StokDarah::where('no_stok', $row['no_stok'])->first();

                $detail = PengembalianDarahExternalDetail::create([
                    'pengembalian_id' => $pengembalian->id,
                    'no_stok'         => $row['no_stok'],
                    'no_kantong'      => $stok?->no_kantong,
                    'stok_darah_id'   => $stok?->id,
                    'jenis_darah'     => $stok?->jenis_darah    ?? $row['jenis_darah']   ?? null,
                    'golongan_darah'  => $stok?->golongan_darah ?? $row['golongan_darah']?? null,
                    'rhesus'          => $stok?->rhesus          ?? $row['rhesus']         ?? null,
                    'tgl_aftap'       => $stok?->tgl_aftap       ?? $row['tgl_aftap']      ?? null,
                    'tgl_expired'     => $stok?->tgl_expired     ?? $row['tgl_expired']    ?? null,
                    'status_stok'     => $stok?->status_stok     ?? null,
                    'status_kembali'  => $row['status_kembali']  ?? null,
                    'alasan_kembali'  => $row['alasan_kembali']  ?? null,
                    'jumlah'          => $row['jumlah']          ?? 1,
                    'keterangan'      => $row['keterangan']      ?? null,
                ]);

                if ($stok) {
                    $stok->increment('jumlah_kembali');
                    $stok->increment('saldo');
                    $stok->update(['status_stok' => 'tersedia']);
                }

                TransaksiStokDarah::create([
                    'no_stok'       => $row['no_stok'],
                    'stok_darah_id' => $stok?->id,
                    'jenis'         => 'kembali',
                    'jumlah'        => $detail->jumlah,
                    'no_referensi'  => $pengembalian->no_pengembalian,
                    'sumber'        => 'pengembalian_external',
                    'referensi_id'  => $pengembalian->id,
                    'keterangan'    => $detail->alasan_kembali,
                    'petugas_id'    => $data['petugas_kembali_id'] ?? null,
                    'created_by'    => Auth::id(),
                ]);
            }

            return $pengembalian->fresh('details');
        });
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(PengembalianDarahExternal $pengembalian): void
    {
        DB::transaction(function () use ($pengembalian) {
            foreach ($pengembalian->details as $detail) {
                $stok = StokDarah::where('no_stok', $detail->no_stok)->first();
                if ($stok) {
                    $stok->decrement('jumlah_kembali');
                    $stok->decrement('saldo');
                }
                TransaksiStokDarah::where('referensi_id', $pengembalian->id)
                    ->where('sumber', 'pengembalian_external')
                    ->where('no_stok', $detail->no_stok)
                    ->delete();
            }
            $pengembalian->details()->delete();
            $pengembalian->delete();
        });
    }

    // ─── Cari Stok ────────────────────────────────────────────────────────────

    public function cariStok(string $noStok): ?StokDarah
    {
        return StokDarah::where('no_stok', $noStok)
            ->whereIn('status_stok', ['keluar', 'dipakai'])
            ->first();
    }
}