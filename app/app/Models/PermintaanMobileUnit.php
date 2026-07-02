<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermintaanMobileUnit extends Model
{
    protected $table = 'permintaan_mobile_unit';

    protected $fillable = [
        'bagian_petugas_id',
        'petugas_id',
        'verifikator_id',
        'keterangan',
        'nomor',
        'tanggal',
        'flag',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'flag'    => 'integer',
    ];

    // ── Relasi ──────────────────────────────────────────────
    public function bagianPetugas(): BelongsTo
    {
        return $this->belongsTo(BagianPetugas::class, 'bagian_petugas_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(Petugas::class, 'petugas_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(Petugas::class, 'verifikator_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PermintaanMobileUnitDetail::class, 'permintaan_mu_id');
    }

    // ── Accessor ─────────────────────────────────────────────
    public function getFlagLabelAttribute(): string
    {
        return match ((int) $this->flag) {
            0 => 'Draft',
            1 => 'Diajukan',
            2 => 'Diverifikasi',
            3 => 'Selesai',
            default => 'Tidak Diketahui',
        };
    }

    public function getFlagColorAttribute(): string
    {
        return match ((int) $this->flag) {
            0 => 'secondary',
            1 => 'warning',
            2 => 'info',
            3 => 'success',
            default => 'dark',
        };
    }

    // ── Scope ────────────────────────────────────────────────
    public function scopeByFlag($query, int $flag)
    {
        return $query->where('flag', $flag);
    }

    public function scopeSearch($query, ?string $keyword)
    {
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('nomor', 'like', "%{$keyword}%")
                  ->orWhere('keterangan', 'like', "%{$keyword}%");
            });
        }
        return $query;
    }
}