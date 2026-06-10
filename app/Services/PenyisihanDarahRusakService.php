<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\PenyisihanDarahRusak;
use App\Models\PenyisihanDarahRusakDetail;
use App\Models\StokDarah;
use App\Models\TransaksiStokDarah;
use Carbon\Carbon;

class PenyisihanDarahRusakService
{
    // ─── Generate Nomor ──────────────────────────────────────────

    public function generateNomor(): string
    {
        $prefix = 'D' . now()->format('dmY');
        $last   = PenyisihanDarahRusak::withTrashed()
            ->where('no_penyisihan', 'like', $prefix . '%')
            ->orderByDesc('no_penyisihan')
            ->value('no_penyisihan');

        $seq = $last ? (int) substr($last, -6) + 1 : 1;

        return $prefix . str_pad($seq, 6, '0', STR_PAD_LEFT);
    }

    // ─── List / DataTable ─────────────────────────────────────────

    public function getData(array $filters = [])
    {
        $query = PenyisihanDarahRusak::with(['petugas', 'details'])
            ->orderByDesc('tgl_penyisihan');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['search'])) {
            $query->where('no_penyisihan', 'like', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['tgl_dari'])) {
            $query->whereDate('tgl_penyisihan', '>=', $filters['tgl_dari']);
        }
        if (!empty($filters['tgl_sampai'])) {
            $query->whereDate('tgl_penyisihan', '<=', $filters['tgl_sampai']);
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    // ─── Find by No Stok ──────────────────────────────────────────

    public function findStok(string $noStok): ?StokDarah
    {
        return StokDarah::with('penerimaan')
            ->where('no_stok', $noStok)
            ->where('status_stok', 'tersedia')
            ->where('saldo', '>', 0)
            ->first();
    }

    // ─── Store ───────────────────────────────────────────────────

    public function store(array $data): PenyisihanDarahRusak
    {
        return DB::transaction(function () use ($data) {
            $header = PenyisihanDarahRusak::create([
                'no_penyisihan' => $this->generateNomor(),
                'tgl_penyisihan'=> $data['tgl_penyisihan'] ?? today(),
                'alasan'        => $data['alasan'],
                'keterangan'    => $data['keterangan'] ?? null,
                'status'        => 'draft',
                'petugas_id'    => $data['petugas_id'] ?? Auth::id(),
                'created_by'    => Auth::id(),
            ]);

            foreach ($data['details'] as $row) {
                $stok = StokDarah::where('no_stok', $row['no_stok'])
                    ->where('status_stok', 'tersedia')
                    ->firstOrFail();

                PenyisihanDarahRusakDetail::create([
                    'penyisihan_id'  => $header->id,
                    'stok_darah_id'  => $stok->id,
                    'penerimaan_id'  => $stok->penerimaan_id,
                    'no_stok'        => $stok->no_stok,
                    'jenis_darah'    => $stok->jenis_darah,
                    'golongan_darah' => $stok->golongan_darah,
                    'rhesus'         => $stok->rhesus,
                    'tgl_aftap'      => $stok->tgl_aftap,
                    'tgl_expired'    => $stok->tgl_expired,
                    'status_detail'  => 'pending',
                ]);
            }

            return $header->load('details');
        });
    }

    // ─── Update ──────────────────────────────────────────────────

    public function update(PenyisihanDarahRusak $penyisihan, array $data): PenyisihanDarahRusak
    {
        if ($penyisihan->status === 'disetujui') {
            throw new \Exception('Data yang sudah disetujui tidak dapat diubah.');
        }

        return DB::transaction(function () use ($penyisihan, $data) {
            $penyisihan->update([
                'tgl_penyisihan' => $data['tgl_penyisihan'] ?? $penyisihan->tgl_penyisihan,
                'alasan'         => $data['alasan'] ?? $penyisihan->alasan,
                'keterangan'     => $data['keterangan'] ?? $penyisihan->keterangan,
            ]);

            if (isset($data['details'])) {
                // Hapus detail lama, simpan yang baru
                $penyisihan->details()->delete();

                foreach ($data['details'] as $row) {
                    $stok = StokDarah::where('no_stok', $row['no_stok'])->firstOrFail();

                    PenyisihanDarahRusakDetail::create([
                        'penyisihan_id'  => $penyisihan->id,
                        'stok_darah_id'  => $stok->id,
                        'penerimaan_id'  => $stok->penerimaan_id,
                        'no_stok'        => $stok->no_stok,
                        'jenis_darah'    => $stok->jenis_darah,
                        'golongan_darah' => $stok->golongan_darah,
                        'rhesus'         => $stok->rhesus,
                        'tgl_aftap'      => $stok->tgl_aftap,
                        'tgl_expired'    => $stok->tgl_expired,
                        'status_detail'  => 'pending',
                    ]);
                }
            }

            return $penyisihan->refresh()->load('details');
        });
    }

    // ─── Approve ────────────────────────────────────────────────

    public function approve(PenyisihanDarahRusak $penyisihan): PenyisihanDarahRusak
    {
        return DB::transaction(function () use ($penyisihan) {
            foreach ($penyisihan->details as $detail) {
                $stok = StokDarah::find($detail->stok_darah_id);
                if ($stok) {
                    // Kurangi saldo & update status stok
                    $stok->jumlah_keluar += 1;
                    $stok->saldo          = $stok->jumlah_masuk - $stok->jumlah_keluar + $stok->jumlah_kembali;
                    $stok->status_stok    = 'dibuang';
                    $stok->save();

                    // Catat transaksi stok
                    TransaksiStokDarah::create([
                        'no_stok'       => $stok->no_stok,
                        'stok_darah_id' => $stok->id,
                        'jenis'         => 'keluar',
                        'jumlah'        => 1,
                        'no_referensi'  => $penyisihan->no_penyisihan,
                        'sumber'        => 'penyisihan_darah_rusak',
                        'referensi_id'  => $penyisihan->id,
                        'keterangan'    => 'Penyisihan: ' . $penyisihan->alasan,
                        'created_by'    => Auth::id(),
                    ]);
                }

                $detail->update(['status_detail' => 'selesai']);
            }

            $penyisihan->update([
                'status'      => 'disetujui',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            return $penyisihan->refresh();
        });
    }

    // ─── Destroy ─────────────────────────────────────────────────

    public function destroy(PenyisihanDarahRusak $penyisihan): void
    {
        if ($penyisihan->status === 'disetujui') {
            throw new \Exception('Data yang sudah disetujui tidak dapat dihapus.');
        }

        DB::transaction(fn () => $penyisihan->delete());
    }
}