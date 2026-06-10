<?php

namespace App\Services;

use App\Models\FraksionasiDarah;
use App\Models\StokDarah;
use App\Models\TransaksiStokDarah;
use App\Models\Petugas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FraksionasiDarahService
{
    // ─── Next Nomor ─────────────────────────────────────────────────────────────

    public function generateNoFraksionasi(): string
    {
        $prefix = 'FRK' . now()->format('Ymd');
        $last   = FraksionasiDarah::withTrashed()
                    ->where('no_fraksionasi', 'like', $prefix . '%')
                    ->orderByDesc('no_fraksionasi')
                    ->value('no_fraksionasi');

        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function generateNoTransaksi(): string
    {
        $prefix = 'FS' . now()->format('Ymd'). 'P';
        $last   = FraksionasiDarah::withTrashed()
                    ->where('no_transaksi', 'like', $prefix . '%')
                    ->orderByDesc('no_transaksi')
                    ->value('no_transaksi');

        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT, );
    }

    // ─── Datatable ──────────────────────────────────────────────────────────────

    public function getData(array $filters)
    {
        $q = FraksionasiDarah::with(['petugas', 'stokDarah', 'pendataanKantong']);

        if (!empty($filters['status'])) {
            $q->where('status', $filters['status']);
        }

        if (!empty($filters['jenis_darah'])) {
            $q->where('jenis_darah', $filters['jenis_darah']);
        }

        if (!empty($filters['golongan_darah'])) {
            $q->where('golongan_darah', $filters['golongan_darah']);
        }

        if (!empty($filters['tgl_dari'])) {
            $q->whereDate('tgl_dropping', '>=', $filters['tgl_dari']);
        }

        if (!empty($filters['tgl_sampai'])) {
            $q->whereDate('tgl_dropping', '<=', $filters['tgl_sampai']);
        }

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $q->where(function ($sub) use ($s) {
                $sub->where('no_fraksionasi', 'like', "%$s%")
                    ->orWhere('no_stok', 'like', "%$s%")
                    ->orWhere('no_kantong', 'like', "%$s%")
                    ->orWhere('no_transaksi', 'like', "%$s%");
            });
        }

        return $q->orderByDesc('tgl_dropping');
    }

    // ─── Store ──────────────────────────────────────────────────────────────────

    public function store(array $data): FraksionasiDarah
    {
        return DB::transaction(function () use ($data) {
            $petugasId = $this->getPetugasId();

            // Ambil data stok darah
            $stok = StokDarah::where('no_stok', $data['no_stok'])->firstOrFail();

            $fraksionasi = FraksionasiDarah::create([
                'no_fraksionasi'      => $this->generateNoFraksionasi(),
                'no_transaksi'        => $this->generateNoTransaksi(),
                'no_stok'             => $data['no_stok'],
                'stok_darah_id'       => $stok->id,
                'pendataan_kantong_id' => $data['pendataan_kantong_id'] ?? $stok->pendataanKantong?->id,

                'jenis_darah'    => $data['jenis_darah']    ?? $stok->jenis_darah,
                'golongan_darah' => $data['golongan_darah'] ?? $stok->golongan_darah,
                'rhesus'         => $data['rhesus']         ?? $stok->rhesus,
                'no_kantong'     => $data['no_kantong']     ?? $stok->no_kantong,

                'ukuran_kantong' => $data['ukuran_kantong'] ?? '450',
                'jenis_kantong'  => $data['jenis_kantong']  ?? null,
                'tipe_kantong'   => $data['tipe_kantong']   ?? null,
                'merk'           => $data['merk']           ?? null,

                'suhu_box'       => $data['suhu_box']       ?? null,
                'tgl_dropping'   => $data['tgl_dropping']   ?? now(),
                'tgl_produksi'   => $data['tgl_produksi']   ?? $stok->tgl_produksi,
                'tgl_kadaluarsa' => $data['tgl_kadaluarsa'] ?? $stok->tgl_expired,

                'nomor_rak'  => $data['nomor_rak']  ?? null,
                'nomor_box'  => $data['nomor_box']  ?? null,
                'status'     => 'proses',
                'keterangan' => $data['keterangan'] ?? null,

                'petugas_id' => $petugasId,
                'created_by' => $petugasId,
                'updated_by' => $petugasId,
            ]);

            // Catat transaksi stok
            $this->catatTransaksi($fraksionasi, $stok, 'keluar', 'fraksionasi');

            return $fraksionasi->load(['petugas', 'stokDarah', 'pendataanKantong']);
        });
    }

    // ─── Update ─────────────────────────────────────────────────────────────────

    public function update(FraksionasiDarah $fraksionasi, array $data): FraksionasiDarah
    {
        return DB::transaction(function () use ($fraksionasi, $data) {
            $petugasId = $this->getPetugasId();

            $fraksionasi->update(array_merge($data, [
                'updated_by' => $petugasId,
            ]));

            return $fraksionasi->fresh(['petugas', 'stokDarah', 'pendataanKantong']);
        });
    }

    // ─── Selesai ────────────────────────────────────────────────────────────────

    public function selesai(FraksionasiDarah $fraksionasi): FraksionasiDarah
    {
        return DB::transaction(function () use ($fraksionasi) {
            $fraksionasi->update([
                'status'     => 'selesai',
                'updated_by' => $this->getPetugasId(),
            ]);

            return $fraksionasi->fresh();
        });
    }

    // ─── Destroy ────────────────────────────────────────────────────────────────

    public function destroy(FraksionasiDarah $fraksionasi): void
    {
        DB::transaction(function () use ($fraksionasi) {
            // Batalkan transaksi stok (kembalikan saldo)
            $stok = $fraksionasi->stokDarah;
            if ($stok) {
                $this->catatTransaksi($fraksionasi, $stok, 'kembali', 'hapus_fraksionasi');
                $stok->decrement('jumlah_keluar');
                $stok->increment('saldo');
            }

            $fraksionasi->delete();
        });
    }

    // ─── Cari Stok ──────────────────────────────────────────────────────────────

   public function cariStok(string $keyword)
{
    try {
        $results = StokDarah::with(['pendataanKantong'])
            ->where('status_stok', 'tersedia')
            ->where(function ($q) use ($keyword) {
                $q->where('no_stok',    'like', "%$keyword%")
                  ->orWhere('no_kantong', 'like', "%$keyword%");
            })
            ->limit(20)
            ->get();

        return $results->map(function ($s) {
            $pk = $s->pendataanKantong; // join via no_kantong = barcode ✓

            return [
                'id'             => $s->id,
                'no_stok'        => $s->no_stok,
                'no_kantong'     => $s->no_kantong,
                'jenis_darah'    => $s->jenis_darah,
                'golongan_darah' => $s->golongan_darah,
                'rhesus'         => $s->rhesus,
                'tgl_aftap'      => $s->tgl_aftap,
                'tgl_expired'    => $s->tgl_expired,
                'tgl_produksi'   => $s->tgl_produksi,
                'gr'             => $s->gr  ?? 0,
                'ml'             => $s->ml  ?? 0,

                // ── dari pendataan_kantong (barcode = no_kantong) ──
                'jenis_kantong'  => $pk?->jenis_kantong,   // "Single"
                'merk'           => $pk?->merk_kantong,    // "Amicore"
                'tipe_kantong'   => $pk?->type_kantong,    // "SG"
                'ukuran'         => $pk?->ukuran ?? 450,   // "350 CC"
            ];
        });

    } catch (\Exception $e) {
        \Log::error('cariStok error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        throw $e;
    }
}

    // ─── Summary ────────────────────────────────────────────────────────────────

    public function getSummary(): array
    {
        return [
            'total'   => FraksionasiDarah::count(),
            'proses'  => FraksionasiDarah::proses()->count(),
            'selesai' => FraksionasiDarah::selesai()->count(),
            'hari_ini' => FraksionasiDarah::whereDate('tgl_dropping', today())->count(),
        ];
    }

    // ─── Private Helpers ────────────────────────────────────────────────────────

    private function getPetugasId(): ?int
    {
        return Petugas::where('user_id', Auth::id())->value('id');
    }

    private function catatTransaksi(FraksionasiDarah $fraksionasi, StokDarah $stok, string $jenis, string $sumber): void
    {
        TransaksiStokDarah::create([
            'no_stok'       => $stok->no_stok,
            'stok_darah_id' => $stok->id,
            'jenis'         => $jenis,
            'jumlah'        => 1,
            'no_referensi'  => $fraksionasi->no_fraksionasi,
            'sumber'        => $sumber,
            'referensi_id'  => $fraksionasi->id,
            'keterangan'    => 'Fraksionasi darah',
            'petugas_id'    => Auth::id(),
            'created_by'    => Auth::id(),
        ]);

        if ($jenis === 'keluar') {
            $stok->increment('jumlah_keluar');
            $stok->decrement('saldo');
        }
    }
}