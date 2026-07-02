<?php

namespace App\Services;

use App\Models\PengirimanDarahExternal;
use App\Models\PengirimanDarahExternalDetail;
use App\Models\PermintaanDarahExternal;
use App\Models\PermintaanDarahExternalDetail;
use App\Models\JenisBiaya;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PengirimanDarahExternalService
{
    public function __construct(
        protected StokDarahService $stokService  
    ) {}

    
    public function generateNomorPengiriman(): string
    {
        $prefix = 'PDE' . now()->format('Ymd');

        $last = PengirimanDarahExternal::where('nomor_pengiriman', 'like', $prefix . '%')
            ->orderByDesc('nomor_pengiriman')
            ->value('nomor_pengiriman');

        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Ambil data permintaan berdasarkan nomor permintaan untuk form pengiriman
     */
    public function getPermintaanByNomor(string $noPermintaan): ?array
    {
        $permintaan = PermintaanDarahExternal::with(['details'])
            ->where('nomor_permintaan', $noPermintaan)
            ->first();

        if (!$permintaan) {
            return null;
        }

        return [
            'id'               => $permintaan->id,
            'nomor_permintaan' => $permintaan->nomor_permintaan,
            'tanggal'          => $permintaan->tanggal
                                    ? $permintaan->tanggal->format('d-m-Y')
                                    : '-',
            'nama_peminta'     => $permintaan->nama_peminta,
            'institusi_lain'   => $permintaan->institusi_lain,
            'jenis_biaya'      => $permintaan->jenis_biaya,
            'dropping'         => $permintaan->dropping,
            'petugas'          => $permintaan->petugas,
            'petugas_kode'     => $permintaan->petugas_kode,
            'status'           => $permintaan->status,
            'keterangan'       => $permintaan->keterangan,
            'details'          => $permintaan->details->map(fn($d) => [
                'id'              => $d->id,
                'jenis_darah'     => $d->jenis_darah,
                'gol_darah'       => $d->gol_darah,
                'rhesus'          => $d->rhesus,
                'jumlah'          => $d->jumlah,
                'jumlah_dipenuhi' => $d->jumlah_dipenuhi ?? 0,
                'sisa'            => $d->jumlah - ($d->jumlah_dipenuhi ?? 0),
                'tanggal_perlu'   => $d->tgl_perlu
                                        ? $d->tgl_perlu->format('d-m-Y')
                                        : '-',
                'donor_pengganti' => $d->donor_pengganti ?? '-',
                'keterangan'      => $d->keterangan,
            ])->values()->toArray(),
        ];
    }

    /**
     * Cari stok tersedia — disesuaikan dengan kolom tabel stok_darah
     * Kolom: no_stok, golongan_darah, rhesus, tgl_expired, status_stok
     */
    public function cariStokTersedia(string $jenisDarah, string $golDarah, string $rhesus): array
    {
        try {
            $stok = DB::table('stok_darah')
                ->where('jenis_darah',     $jenisDarah)
                ->where('golongan_darah',  $golDarah)   
                ->where('rhesus',          $rhesus)          
                ->where('status_stok',     'tersedia')   
                ->where('saldo',           '>', 0)       
                ->whereDate('tgl_expired', '>=', now())  
                ->orderBy('tgl_expired')
                ->get([
                    'id',
                    'no_stok',           
                    'jenis_darah',
                    'golongan_darah',   
                    'rhesus',            
                    'tgl_expired',      
                    'saldo',
                    'no_kantong',
                    'skrining',
                ]);

            // Normalkan ke format yang dipakai frontend (no_stock, gol_darah, rh, tgl_kadaluarsa)
            return $stok->map(fn($s) => [
                'id'            => $s->id,
                'no_stock'      => $s->no_stok,
                'jenis_darah'   => $s->jenis_darah,
                'gol_darah'     => $s->golongan_darah,
                'rhesus'        => $s->rhesus,
                'tgl_kadaluarsa'=> $s->tgl_expired,
                'saldo'         => $s->saldo,
                'no_kantong'    => $s->no_kantong,
                'nat'           => $s->skrining === 'NAT' ? true : false,
            ])->toArray();

        } catch (\Exception $e) {
            Log::error('cariStokTersedia error: ' . $e->getMessage());
            return [];
        }
    }

     public function getJenisBiaya()
    {
        return JenisBiaya::select('id', 'kode', 'nama')
            ->orderBy('nama')
            ->get();
    }
    public function store(array $data): PengirimanDarahExternal
    {
        return DB::transaction(function () use ($data) {
            $pengiriman = PengirimanDarahExternal::create([
                'nomor_pengiriman' => $this->generateNomorPengiriman(),
                'permintaan_id'    => $data['permintaan_id'],
                'no_permintaan'    => $data['no_permintaan'],
                'tanggal_kirim'    => $data['tanggal_kirim'] ?? now(),
                'petugas'          => $data['petugas'],
                'petugas_kode'     => $data['petugas_kode'],
                'penerima'         => $data['penerima'] ?? null,
                'institusi_tujuan' => $data['institusi_tujuan'] ?? null,
                'jenis_biaya'      => $data['jenis_biaya'],
                'dropping'         => $data['dropping'] ?? null,
                'suhu_kirim'       => $data['suhu_kirim'] ?? null,
                'keterangan'       => $data['keterangan'] ?? null,
                'status'           => 'PROSES',
            ]);

            foreach ($data['details'] as $detail) {
                // Ambil data stok berdasarkan no_stok
                $stok = DB::table('stok_darah')
                    ->where('no_stok', $detail['no_stock'])
                    ->lockForUpdate()
                    ->first();

                if (!$stok) {
                    throw new \Exception("Stok {$detail['no_stock']} tidak ditemukan.");
                }
                if ($stok->saldo < 1) {
                    throw new \Exception("Saldo stok {$detail['no_stock']} tidak mencukupi.");
                }

                $jumlah = $detail['jumlah'] ?? 1;

                // Simpan detail pengiriman
                PengirimanDarahExternalDetail::create([
                    'pengiriman_id'        => $pengiriman->id,
                    'permintaan_detail_id' => $detail['permintaan_detail_id'] ?? null,
                    'no_stock'             => $detail['no_stock'],
                    'jenis_darah'          => $detail['jenis_darah'],
                    'gol_darah'            => $detail['gol_darah'],
                    'rhesus'               => $detail['rhesus'],
                    'jumlah'               => $jumlah,
                    'tgl_kadaluarsa'       => $detail['tgl_kadaluarsa'] ?? null,
                    'nat'                  => $detail['nat'] ?? false,
                    'keterangan'           => $detail['keterangan'] ?? null,
                ]);

                // Update jumlah_dipenuhi di detail permintaan
                if (!empty($detail['permintaan_detail_id'])) {
                    PermintaanDarahExternalDetail::where('id', $detail['permintaan_detail_id'])
                        ->increment('jumlah_dipenuhi', $jumlah);
                }

                // Update saldo & status stok_darah
                $sisaSaldo = $stok->saldo - $jumlah;
                DB::table('stok_darah')
                    ->where('no_stok', $detail['no_stock'])
                    ->update([
                        'jumlah_keluar' => DB::raw("jumlah_keluar + {$jumlah}"),
                        'saldo'         => DB::raw("saldo - {$jumlah}"),
                        'status_stok'   => $sisaSaldo <= 0 ? 'dipakai' : 'tersedia',
                        'updated_at'    => now(),
                    ]);

                // Catat ke transaksi_stok_darah
                DB::table('transaksi_stok_darah')->insert([
                    'no_stok'       => $detail['no_stock'],
                    'stok_darah_id' => $stok->id,
                    'jenis'         => 'keluar',
                    'jumlah'        => $jumlah,
                    'no_referensi'  => $pengiriman->nomor_pengiriman,
                    'sumber'        => 'pengiriman_external',
                    'referensi_id'  => $pengiriman->id,
                    'keterangan'    => 'Pengiriman darah external ke ' . ($data['institusi_tujuan'] ?? '-'),
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            $this->updateStatusPermintaan($data['permintaan_id']);

            return $pengiriman->load('details');
        });
    }

    /**
     * Update pengiriman yang sudah ada
     */
    public function update(int $id, array $data): PengirimanDarahExternal
    {
        return DB::transaction(function () use ($id, $data) {
            $pengiriman = PengirimanDarahExternal::with('details')->findOrFail($id);

            // Rollback stok lama
            foreach ($pengiriman->details as $oldDetail) {
                $jumlahLama = $oldDetail->jumlah;

                DB::table('stok_darah')
                    ->where('no_stok', $oldDetail->no_stock)
                    ->update([
                        'jumlah_keluar' => DB::raw("jumlah_keluar - {$jumlahLama}"),
                        'saldo'         => DB::raw("saldo + {$jumlahLama}"),
                        'status_stok'   => 'tersedia',
                        'updated_at'    => now(),
                    ]);

                // Catat transaksi kembali
                $stok = DB::table('stok_darah')->where('no_stok', $oldDetail->no_stock)->first();
                DB::table('transaksi_stok_darah')->insert([
                    'no_stok'       => $oldDetail->no_stock,
                    'stok_darah_id' => $stok?->id,
                    'jenis'         => 'kembali',
                    'jumlah'        => $jumlahLama,
                    'no_referensi'  => $pengiriman->nomor_pengiriman,
                    'sumber'        => 'pengiriman_external',
                    'referensi_id'  => $pengiriman->id,
                    'keterangan'    => 'Rollback edit pengiriman external',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);

                if ($oldDetail->permintaan_detail_id) {
                    PermintaanDarahExternalDetail::where('id', $oldDetail->permintaan_detail_id)
                        ->decrement('jumlah_dipenuhi', $jumlahLama);
                }
            }

            // Hapus detail lama
            $pengiriman->details()->delete();

            // Update header
            $pengiriman->update([
                'tanggal_kirim'    => $data['tanggal_kirim']    ?? $pengiriman->tanggal_kirim,
                'petugas'          => $data['petugas']          ?? $pengiriman->petugas,
                'petugas_kode'     => $data['petugas_kode']     ?? $pengiriman->petugas_kode,
                'penerima'         => $data['penerima']         ?? $pengiriman->penerima,
                'institusi_tujuan' => $data['institusi_tujuan'] ?? $pengiriman->institusi_tujuan,
                'jenis_biaya'      => $data['jenis_biaya']      ?? $pengiriman->jenis_biaya,
                'dropping'         => $data['dropping']         ?? $pengiriman->dropping,
                'suhu_kirim'       => $data['suhu_kirim']       ?? $pengiriman->suhu_kirim,
                'keterangan'       => $data['keterangan']       ?? $pengiriman->keterangan,
            ]);

            // Simpan detail baru + update stok baru
            foreach ($data['details'] as $detail) {
                $stok = DB::table('stok_darah')
                    ->where('no_stok', $detail['no_stock'])
                    ->lockForUpdate()
                    ->first();

                if (!$stok) {
                    throw new \Exception("Stok {$detail['no_stock']} tidak ditemukan.");
                }
                if ($stok->saldo < 1) {
                    throw new \Exception("Saldo stok {$detail['no_stock']} tidak mencukupi.");
                }

                $jumlah = $detail['jumlah'] ?? 1;

                PengirimanDarahExternalDetail::create([
                    'pengiriman_id'        => $pengiriman->id,
                    'permintaan_detail_id' => $detail['permintaan_detail_id'] ?? null,
                    'no_stock'             => $detail['no_stock'],
                    'jenis_darah'          => $detail['jenis_darah'],
                    'gol_darah'            => $detail['gol_darah'],
                    'rhesus'               => $detail['rhesus'],
                    'jumlah'               => $jumlah,
                    'tgl_kadaluarsa'       => $detail['tgl_kadaluarsa'] ?? null,
                    'nat'                  => $detail['nat'] ?? false,
                    'keterangan'           => $detail['keterangan'] ?? null,
                ]);

                if (!empty($detail['permintaan_detail_id'])) {
                    PermintaanDarahExternalDetail::where('id', $detail['permintaan_detail_id'])
                        ->increment('jumlah_dipenuhi', $jumlah);
                }

                $sisaSaldo = $stok->saldo - $jumlah;
                DB::table('stok_darah')
                    ->where('no_stok', $detail['no_stock'])
                    ->update([
                        'jumlah_keluar' => DB::raw("jumlah_keluar + {$jumlah}"),
                        'saldo'         => DB::raw("saldo - {$jumlah}"),
                        'status_stok'   => $sisaSaldo <= 0 ? 'dipakai' : 'tersedia',
                        'updated_at'    => now(),
                    ]);

                DB::table('transaksi_stok_darah')->insert([
                    'no_stok'       => $detail['no_stock'],
                    'stok_darah_id' => $stok->id,
                    'jenis'         => 'keluar',
                    'jumlah'        => $jumlah,
                    'no_referensi'  => $pengiriman->nomor_pengiriman,
                    'sumber'        => 'pengiriman_external',
                    'referensi_id'  => $pengiriman->id,
                    'keterangan'    => 'Edit pengiriman darah external',
                    'petugas_id'    => Auth::id(),
                    'created_by'    => Auth::id(),
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            $this->updateStatusPermintaan($pengiriman->permintaan_id);

            return $pengiriman->load('details');
        });
    }

    /**
     * Hapus pengiriman dan kembalikan stok
     */
    public function destroy(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $pengiriman = PengirimanDarahExternal::with('details')->findOrFail($id);

            foreach ($pengiriman->details as $detail) {
                $jumlah = $detail->jumlah;

                DB::table('stok_darah')
                    ->where('no_stok', $detail->no_stock)
                    ->update([
                        'jumlah_keluar' => DB::raw("jumlah_keluar - {$jumlah}"),
                        'saldo'         => DB::raw("saldo + {$jumlah}"),
                        'status_stok'   => 'tersedia',
                        'updated_at'    => now(),
                    ]);

                $stok = DB::table('stok_darah')->where('no_stok', $detail->no_stock)->first();
                DB::table('transaksi_stok_darah')->insert([
                    'no_stok'       => $detail->no_stock,
                    'stok_darah_id' => $stok?->id,
                    'jenis'         => 'kembali',
                    'jumlah'        => $jumlah,
                    'no_referensi'  => $pengiriman->nomor_pengiriman,
                    'sumber'        => 'pengiriman_external',
                    'referensi_id'  => $pengiriman->id,
                    'keterangan'    => 'Rollback: pengiriman external dihapus',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);

                if ($detail->permintaan_detail_id) {
                    PermintaanDarahExternalDetail::where('id', $detail->permintaan_detail_id)
                        ->decrement('jumlah_dipenuhi', $jumlah);
                }
            }

            $permintaanId = $pengiriman->permintaan_id;
            $pengiriman->delete();

            $this->updateStatusPermintaan($permintaanId);

            return true;
        });
    }

    /**
     * Update status permintaan berdasarkan jumlah terpenuhi
     */
    protected function updateStatusPermintaan(int $permintaanId): void
    {
        $permintaan = PermintaanDarahExternal::with('details')->find($permintaanId);
        if (!$permintaan) return;

        $totalJumlah   = $permintaan->details->sum('jumlah');
        $totalDipenuhi = $permintaan->details->sum('jumlah_dipenuhi');

        if ($totalDipenuhi === 0) {
            $status = 'BELUM_DIPENUHI';
        } elseif ($totalDipenuhi >= $totalJumlah) {
            $status = 'SUDAH_DIPENUHI';
        } else {
            $status = 'SEBAGIAN';
        }

        $permintaan->update(['status' => $status]);
    }

    /**
     * Ambil data pengiriman untuk DataTable
     */
    public function getData(array $filters = []): \Illuminate\Database\Eloquent\Builder
    {
        $query = PengirimanDarahExternal::with(['permintaan', 'details'])
            ->orderByDesc('tanggal_kirim');

        if (!empty($filters['no_permintaan'])) {
            $query->where('no_permintaan', 'like', '%' . $filters['no_permintaan'] . '%');
        }
        if (!empty($filters['tanggal_dari'])) {
            $query->whereDate('tanggal_kirim', '>=', $filters['tanggal_dari']);
        }
        if (!empty($filters['tanggal_sampai'])) {
            $query->whereDate('tanggal_kirim', '<=', $filters['tanggal_sampai']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }
}