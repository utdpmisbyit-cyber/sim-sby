<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrossTestReferal extends Model
{
    use SoftDeletes;

    protected $table = 'cross_tests_referal';

    protected $fillable = [
        'permintaan_fpup_referal_id',
        'permintaan_fpup_referal_detail_id',
        'no_fpup',
        'no_referal',
        'nama_pasien',
        'gol',
        'rhesus',
        'no_stock',
        'jenis_darah_id',
        'jns_darah',
        'gol_rh_kantong',
        'tgl_ambil',
        'tgl_produksi',
        'tgl_kadaluarsa',
        'tgl_online',
        'referal',
        'kurir_online',
        'catatan_hasil',
        'pemeriksa',
        'tgl_periksa',
        'status',
    ];

    protected $casts = [
        'tgl_ambil'      => 'date',
        'tgl_produksi'   => 'date',
        'tgl_kadaluarsa' => 'date',
        'tgl_periksa'    => 'datetime',
        'tgl_online'     => 'date',
    ];

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'compatible'   => 'success',
            'incompatible' => 'danger',
            'proses'       => 'warning',
            'selesai'      => 'info',
            default        => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'      => 'Pending',
            'proses'       => 'Proses',
            'compatible'   => 'Compatible',
            'incompatible' => 'Incompatible',
            'selesai'      => 'Selesai',
            default        => ucfirst($this->status),
        };
    }

    public function getIsKadaluarsaAttribute(): bool
    {
        return $this->tgl_kadaluarsa && $this->tgl_kadaluarsa->isPast();
    }

    public function permintaanFpupReferal(): BelongsTo
    {
        return $this->belongsTo(PermintaanFpupReferal::class, 'permintaan_fpup_referal_id');
    }

    public function permintaanFpupReferalDetail(): BelongsTo
    {
        return $this->belongsTo(PermintaanFpupReferalDetail::class, 'permintaan_fpup_referal_detail_id');
    }

    public function jenisDarah(): BelongsTo
    {
        return $this->belongsTo(JenisDarah::class, 'jenis_darah_id');
    }

    public function scopeByFpup($query, string $noFpup)
    {
        return $query->where('no_fpup', $noFpup);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}