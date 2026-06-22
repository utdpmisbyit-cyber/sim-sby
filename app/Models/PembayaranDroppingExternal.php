<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranDroppingExternal extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_dropping_external';

    protected $fillable = [
        'pengiriman_id',
        'nomor_kirim',
        'tanggal_kirim',
        'institusi_tujuan',
        'jenis_biaya',
        'harus_dibayar',
        'pembayaran',
        'metode_bayar',
        'tanggal_bayar',
        'kode_kasir',
        'nama_kasir',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'tanggal_kirim' => 'datetime',
        'tanggal_bayar' => 'datetime',
        'harus_dibayar' => 'decimal:2',
        'pembayaran'    => 'decimal:2',
    ];

    /**
     * Pengiriman darah external yang dibayar pada transaksi ini.
     */
    public function pengiriman()
    {
        return $this->belongsTo(PengirimanDarahExternal::class, 'pengiriman_id');
    }

    /**
     * Sisa tagihan (positif = masih kurang bayar / kredit).
     */
    public function getSisaAttribute()
    {
        return (float) $this->harus_dibayar - (float) $this->pembayaran;
    }

    public function getIsLunasAttribute(): bool
    {
        return (float) $this->pembayaran >= (float) $this->harus_dibayar;
    }

    public function scopeCari($query, ?string $keyword)
    {
        if (! $keyword) {
            return $query;
        }

        return $query->where(function ($q) use ($keyword) {
            $q->where('nomor_kirim', 'like', "%{$keyword}%")
              ->orWhere('institusi_tujuan', 'like', "%{$keyword}%")
              ->orWhere('kode_kasir', 'like', "%{$keyword}%");
        });
    }
}
