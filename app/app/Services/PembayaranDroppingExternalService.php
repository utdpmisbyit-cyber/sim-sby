<?php

namespace App\Services;

use App\Models\JenisBiaya;
use App\Models\PembayaranDroppingExternal;
use App\Models\PengirimanDarahExternal;
use App\Models\ServiceCost;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PembayaranDroppingExternalService
{
    public function __construct(
        protected PembayaranDroppingExternal $model,
    ) {
    }

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->cari($filters['search'] ?? null)
            ->when($filters['dari'] ?? null, fn ($q, $v) => $q->whereDate('tanggal_bayar', '>=', $v))
            ->when($filters['sampai'] ?? null, fn ($q, $v) => $q->whereDate('tanggal_bayar', '<=', $v))
            ->orderByDesc('tanggal_bayar');

        return $query->paginate($filters['per_page'] ?? 10);
    }

    public function find(int $id): PembayaranDroppingExternal
    {
        return $this->model->newQuery()->with('pengiriman.details')->findOrFail($id);
    }

    /**
     * @throws ValidationException
     */
    public function cariPengiriman(string $nomorKirim): array
    {
        $pengiriman = PengirimanDarahExternal::query()
            ->with('details')
            ->where('nomor_pengiriman', $nomorKirim)
            ->first();

        if (! $pengiriman) {
            throw ValidationException::withMessages([
                'nomor_kirim' => "Nomor kirim {$nomorKirim} tidak ditemukan.",
            ]);
        }

        $sudahDibayar = $this->model->newQuery()
            ->where('pengiriman_id', $pengiriman->id)
            ->where('status', '!=', 'batal')
            ->exists();

        if ($sudahDibayar) {
            throw ValidationException::withMessages([
                'nomor_kirim' => "Nomor kirim {$nomorKirim} sudah pernah dibayarkan.",
            ]);
        }

        $hasil = $this->buildItemsForPengiriman($pengiriman);

        return [
            'pengiriman'    => $pengiriman,
            'items'         => $hasil['items'],
            'harus_dibayar' => $hasil['harus_dibayar'],
            'jenis_biaya'   => $hasil['jenis_biaya'], // nama jenis biaya, untuk ditampilkan di form
        ];
    }

    /**
     * Susun daftar kantong darah + tarif, dan resolve nama Jenis Biaya-nya.
     *
     * jenis_biaya diambil dari kolom `jenis_biaya` milik pengiriman itu
     * sendiri (mis. "011"), dicocokkan ke master `jenis_biaya`, lalu
     * dipakai untuk filter `service_cost` (dicocokkan lagi ke institusi_tujuan).
     *
     * Dipakai baik saat scan (cariPengiriman) maupun saat edit.
     *
     * @throws ValidationException
     */
    public function buildItemsForPengiriman(PengirimanDarahExternal $pengiriman): array
    {
        $pengiriman->loadMissing('details');

        if (empty($pengiriman->jenis_biaya)) {
            throw ValidationException::withMessages([
                'nomor_kirim' => "Pengiriman {$pengiriman->nomor_pengiriman} belum memiliki Jenis Biaya. Mohon lengkapi data pengiriman terlebih dahulu.",
            ]);
        }

        $jenisBiaya = JenisBiaya::query()
            ->where('kode', $pengiriman->jenis_biaya)
            ->orWhere('nama', $pengiriman->jenis_biaya)
            ->first();

        if (! $jenisBiaya) {
            throw ValidationException::withMessages([
                'nomor_kirim' => "Master Jenis Biaya '{$pengiriman->jenis_biaya}' belum terdaftar. Mohon periksa data master Jenis Biaya.",
            ]);
        }

        $rate = $this->getDroppingRate($jenisBiaya, $pengiriman->institusi_tujuan);

        $items = $pengiriman->details->map(function ($d) use ($pengiriman, $rate) {
            return [
                'tgl_kirim'   => optional($pengiriman->tanggal_kirim)->format('d/m/Y'),
                'no_kirim'    => $pengiriman->nomor_pengiriman,
                'no_stock'    => $d->no_stock,
                'jenis_darah' => $d->jenis_darah,
                'gol_rhesus'  => trim(($d->gol_darah ?? '').($d->rhesus ?? '')),
                'nama_tujuan' => $pengiriman->institusi_tujuan,
                'tarif'       => $rate,
            ];
        })->values();

        return [
            'items'         => $items,
            'harus_dibayar' => $items->sum('tarif'),
            'jenis_biaya'   => $jenisBiaya->nama,
        ];
    }

    /**
     * Ambil tarif dari service_cost milik jenis_biaya yang diberikan,
     * dicocokkan dengan institusi tujuan (kode/nama, exact atau partial).
     * Fallback ke baris `jenis` NULL (tarif umum) jika tidak ada yang spesifik.
     *
     * @throws ValidationException jika service_cost belum diatur sama sekali
     */
    protected function getDroppingRate(JenisBiaya $jenisBiaya, ?string $institusiTujuan = null): int
    {
        $base = ServiceCost::query()->where('jenis_biaya_id', $jenisBiaya->id);

        if ($institusiTujuan) {
            $specific = (clone $base)
                ->where(function ($q) use ($institusiTujuan) {
                    $q->where('kode', $institusiTujuan)
                      ->orWhere('nama', $institusiTujuan)
                      ->orWhere('kode', 'like', "%{$institusiTujuan}%")
                      ->orWhere('nama', 'like', "%{$institusiTujuan}%");
                })
                ->first();

            if ($specific) {
                return (int) $specific->biaya;
            }
        }

        $umum = (clone $base)->whereNull('jenis')->orderByDesc('id')->first()
            ?? $base->orderByDesc('id')->first();

        if (! $umum) {
            throw ValidationException::withMessages([
                'nomor_kirim' => "Tarif untuk jenis biaya '{$jenisBiaya->nama}' belum diatur di master Service Cost.",
            ]);
        }

        return (int) $umum->biaya;
    }

    public function store(array $data): PembayaranDroppingExternal
    {
        return DB::transaction(function () use ($data) {
            $pengiriman = PengirimanDarahExternal::findOrFail($data['pengiriman_id']);

            return $this->model->newQuery()->create([
                'pengiriman_id'    => $pengiriman->id,
                'nomor_kirim'      => $pengiriman->nomor_pengiriman,
                'tanggal_kirim'    => $pengiriman->tanggal_kirim,
                'institusi_tujuan' => $pengiriman->institusi_tujuan,
                'jenis_biaya'      => $data['jenis_biaya'] ?? $pengiriman->jenis_biaya,
                'harus_dibayar'    => $data['harus_dibayar'],
                'pembayaran'       => $data['pembayaran'],
                'metode_bayar'     => $data['metode_bayar'],
                'tanggal_bayar'    => $data['tanggal_bayar'],
                'kode_kasir'       => $data['kode_kasir'] ?? Auth::user()?->kode ?? 'ADM',
                'nama_kasir'       => $data['nama_kasir'] ?? Auth::user()?->name ?? 'Administrator',
                'keterangan'       => $data['keterangan'] ?? null,
                'status'           => (float) $data['pembayaran'] >= (float) $data['harus_dibayar']
                    ? 'lunas'
                    : 'belum_lunas',
            ]);
        });
    }

    public function update(int $id, array $data): PembayaranDroppingExternal
    {
        $pembayaran = $this->model->newQuery()->findOrFail($id);

        $pembayaran->update([
            'jenis_biaya'   => $data['jenis_biaya'] ?? $pembayaran->jenis_biaya,
            'harus_dibayar' => $data['harus_dibayar'],
            'pembayaran'    => $data['pembayaran'],
            'metode_bayar'  => $data['metode_bayar'],
            'tanggal_bayar' => $data['tanggal_bayar'],
            'keterangan'    => $data['keterangan'] ?? null,
            'status'        => (float) $data['pembayaran'] >= (float) $data['harus_dibayar']
                ? 'lunas'
                : 'belum_lunas',
        ]);

        return $pembayaran;
    }

    public function batalkan(int $id): PembayaranDroppingExternal
    {
        $pembayaran = $this->model->newQuery()->findOrFail($id);
        $pembayaran->update(['status' => 'batal']);

        return $pembayaran;
    }

    public function delete(int $id): void
    {
        $this->model->newQuery()->findOrFail($id)->delete();
    }
}