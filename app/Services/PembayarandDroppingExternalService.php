<?php

namespace App\Services;

use App\Models\PembayaranDroppingExternal;
use App\Models\PengirimanDarahExternal;
use App\Models\PengirimanDarahExternalDetail;
use App\Models\JenisBiaya;
use App\Models\ServiceCost;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PembayaranDroppingExternalService
{
 
    public function cariKiriman(string $nomorKirim): array
    {
        /** @var PengirimanDarahExternal|null $pengiriman */
        $pengiriman = PengirimanDarahExternal::with('details')
            ->where('nomor_pengiriman', $nomorKirim)
            ->first();

        if (! $pengiriman) {
            throw new ModelNotFoundException("Nomor kirim {$nomorKirim} tidak ditemukan.");
        }

        if ($pengiriman->status === 'BATAL') {
            throw new RuntimeException("Pengiriman {$nomorKirim} sudah dibatalkan dan tidak dapat ditagihkan.");
        }

        if ($pengiriman->details->isEmpty()) {
            throw new RuntimeException("Pengiriman {$nomorKirim} belum memiliki detail kantong darah.");
        }

        $sudahLunas = PembayaranDroppingExternal::where('pengiriman_id', $pengiriman->id)
            ->where('status', 'LUNAS')
            ->first();

        if ($sudahLunas) {
            $tgl = optional($sudahLunas->tanggal_bayar)->format('d-m-Y H:i');

            throw new RuntimeException(
                "Pengiriman {$nomorKirim} sudah dibayar pada {$tgl} (No. Pembayaran: {$sudahLunas->nomor_pembayaran})."
            );
        }

        [$kode, $nama] = $this->parseInstitusi($pengiriman->institusi_tujuan);

        $details = $pengiriman->details->values()->map(function (PengirimanDarahExternalDetail $d) use ($kode, $nama, $pengiriman) {
            return [
                'pengiriman_detail_id' => $d->id,
                'no_kirim' => $pengiriman->nomor_pengiriman,
                'no_stock' => $d->no_stock,
                'jenis_darah' => $d->jenis_darah,
                'gol_darah' => $d->gol_darah,
                'rhesus' => $d->rhesus,
                'kode_rs' => $kode,
                'nama_rs' => $nama,
                'jenis_biaya' => $pengiriman->jenis_biaya,
                'harga_satuan' => $this->hitungHargaSatuan($pengiriman, $d),
            ];
        });

        return [
            'pengiriman_id' => $pengiriman->id,
            'nomor_kirim' => $pengiriman->nomor_pengiriman,
            'tanggal_kirim' => optional($pengiriman->tanggal_kirim)->toDateTimeString(),
            'institusi_kode' => $kode,
            'institusi_nama' => $nama,
            'jenis_biaya' => $pengiriman->jenis_biaya,
            'harus_dibayar' => (float) $details->sum('harga_satuan'),
            'details' => $details,
        ];
    }

 
    public function simpan(array $payload): PembayaranDroppingExternal
    {
        return DB::transaction(function () use ($payload) {
            $cari = $this->cariKiriman($payload['nomor_kirim']);

            $pembayaran = PembayaranDroppingExternal::create([
                'nomor_pembayaran' => $this->generateNomorPembayaran(),
                'pengiriman_id' => $cari['pengiriman_id'],
                'nomor_kirim' => $cari['nomor_kirim'],
                'tanggal_kirim' => $cari['tanggal_kirim'],
                'institusi_kode' => $cari['institusi_kode'],
                'institusi_nama' => $cari['institusi_nama'],
                'jenis_biaya' => $cari['jenis_biaya'],
                'harus_dibayar' => $cari['harus_dibayar'],
                'jenis_pembayaran' => $payload['jenis_pembayaran'] ?? 'TUNAI',
                'jumlah_bayar' => $payload['jumlah_bayar'],
                'tanggal_bayar' => $payload['tanggal_bayar'] ?? Carbon::now(),
                'kasir_kode' => $payload['kasir_kode'],
                'kasir_nama' => $payload['kasir_nama'],
                'keterangan' => $payload['keterangan'] ?? null,
                'status' => 'LUNAS',
                'created_by' => $payload['created_by'] ?? null,
            ]);

            foreach ($cari['details'] as $d) {
                $pembayaran->details()->create([
                    'pengiriman_detail_id' => $d['pengiriman_detail_id'],
                    'no_stock' => $d['no_stock'],
                    'jenis_darah' => $d['jenis_darah'],
                    'gol_darah' => $d['gol_darah'],
                    'rhesus' => $d['rhesus'],
                    'harga_satuan' => $d['harga_satuan'],
                ]);
            }

            return $pembayaran->load('details');
        });
    }

    /**
     * Setara tombol "F9 - Batal" untuk transaksi yang sudah tersimpan.
     */
    public function batalkan(PembayaranDroppingExternal $pembayaran): PembayaranDroppingExternal
    {
        if ($pembayaran->status === 'BATAL') {
            throw new RuntimeException('Pembayaran ini sudah dibatalkan sebelumnya.');
        }

        $pembayaran->update(['status' => 'BATAL']);

        return $pembayaran;
    }


    protected function parseInstitusi(?string $institusi): array
    {
        if (! $institusi) {
            return [null, null];
        }

        $institusi = trim($institusi);

        if (preg_match('/^(\S+)\s+(.+)$/', $institusi, $m)) {
            return [$m[1], $m[2]];
        }

        return [null, $institusi];
    }

    
    protected function hitungHargaSatuan(PengirimanDarahExternal $pengiriman, PengirimanDarahExternalDetail $detail): float
    {
        $jenisBiaya = $this->resolveJenisBiaya($pengiriman->jenis_biaya);

        $base = ServiceCost::query();

        if ($jenisBiaya) {
            $base->where('jenis_biaya_id', $jenisBiaya->id);
        }

        // Coba cocokkan komponen darah spesifik (mis. "WB") ke kode/nama tarif.
        $kandidat = (clone $base)
            ->where(function ($q) use ($detail) {
                if (! empty($detail->jenis_darah)) {
                    $q->where('kode', $detail->jenis_darah)
                        ->orWhere('nama', 'like', '%'.$detail->jenis_darah.'%');
                }
                if (! empty($detail->no_stock)) {
                    $q->orWhere('kode', $detail->no_stock);
                }
            })
            ->orderBy('id')
            ->first();

        if ($kandidat) {
            return (float) $kandidat->biaya;
        }

        if ($jenisBiaya) {
            $any = (clone $base)->orderBy('id')->first();
            if ($any) {
                return (float) $any->biaya;
            }
        }

        return (float) config('dropping.tarif_default', 250000);
    }

    /**
     * Cocokkan string jenis_biaya milik pengiriman ke baris master `jenis_biaya`.
     * Hasilnya di-cache per-instance agar tidak query berulang untuk tiap detail.
     */
    protected array $jenisBiayaCache = [];

    protected function resolveJenisBiaya(?string $jenisBiaya): ?JenisBiaya
    {
        if (! $jenisBiaya) {
            return null;
        }

        $key = mb_strtolower(trim($jenisBiaya));

        if (array_key_exists($key, $this->jenisBiayaCache)) {
            return $this->jenisBiayaCache[$key];
        }

        $found = JenisBiaya::whereRaw('LOWER(nama) = ?', [$key])
            ->orWhere('kode', trim($jenisBiaya))
            ->first();

        return $this->jenisBiayaCache[$key] = $found;
    }

    /**
     * Format: PDE + YYMMDD + nomor urut 4 digit per hari. Contoh: PDE2606230001
     */
    protected function generateNomorPembayaran(): string
    {
        $prefix = 'PDE'.now()->format('ymd');

        $last = PembayaranDroppingExternal::where('nomor_pembayaran', 'like', "{$prefix}%")
            ->orderByDesc('nomor_pembayaran')
            ->lockForUpdate()
            ->value('nomor_pembayaran');

        $urut = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix.str_pad((string) $urut, 4, '0', STR_PAD_LEFT);
    }
}