<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermintaanMobileUnitDetail extends Model
{
    protected $table = 'permintaan_mobile_unit_detail';

    protected $fillable = [
        'permintaan_mu_id',
        'tipe_kantong_id',
        'jumlah',
        'jumlah_dilayani',
        'kode',
        'merk',
        'jenis',
        'ukuran',
        'status',
        'flag',
    ];

    protected $casts = [
        'jumlah'          => 'integer',
        'jumlah_dilayani' => 'integer',
        'flag'            => 'integer',
    ];

    // ── Relasi ──────────────────────────────────────────────
    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(PermintaanMobileUnit::class, 'permintaan_mu_id');
    }

    public function tipeKantong(): BelongsTo
    {
        return $this->belongsTo(TipeKantong::class, 'tipe_kantong_id');
    }

    // ── Accessor ─────────────────────────────────────────────
    public function getSisaAttribute(): int
    {
        return max(0, $this->jumlah - $this->jumlah_dilayani);
    }
}