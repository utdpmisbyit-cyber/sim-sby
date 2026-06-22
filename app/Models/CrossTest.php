<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class CrossTest extends Model
{
    use SoftDeletes;

    protected $table = 'cross_tests';

    protected $fillable = [
        'permintaan_fpup_id',
        'no_fpup',
        'nama_pasien',
        'gol',
        'rhesus',
        'no_stock',
        'jns_darah',
        'gol_rh_kantong',
        'tgl_ambil',
        'tgl_produksi',
        'tgl_kadaluarsa',
        'tgl_online',
        'referal',
        'no_referal',
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

    // ── Accessors ──────────────────────────────────────────────
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

    public function getIsKadaluarsaAttribute(): bool
    {
        return $this->tgl_kadaluarsa && $this->tgl_kadaluarsa->isPast();
    }

    // ── Relations ─────────────────────────────────────────────
    public function permintaanFpup(): BelongsTo
    {
        return $this->belongsTo(PermintaanFpup::class, 'permintaan_fpup_id');
    }

    // ── Scopes ────────────────────────────────────────────────
    public function scopeByFpup($query, string $noFpup)
    {
        return $query->where('no_fpup', $noFpup);
    }
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
     public function pelayananCrosstests(): HasMany
    {
        return $this->hasMany(PelayananCrosstest::class, 'cross_test_id');
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
}