<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenyisihanDarahRusakDetail extends Model
{
    protected $table = 'penyisihan_darah_rusak_detail';

    protected $fillable = [
        'penyisihan_id',
        'stok_darah_id',
        'penerimaan_id',
        'no_stok',
        'jenis_darah',
        'golongan_darah',
        'rhesus',
        'tgl_aftap',
        'tgl_expired',
        'status_detail',
    ];

    protected $casts = [
        'tgl_aftap'    => 'date',
        'tgl_expired'  => 'date',
    ];

    // ─── Relationships ───────────────────────────────────────────

    public function penyisihan(): BelongsTo
    {
        return $this->belongsTo(PenyisihanDarahRusak::class, 'penyisihan_id');
    }

    public function stokDarah(): BelongsTo
    {
        return $this->belongsTo(StokDarah::class, 'stok_darah_id');
    }

    public function penerimaan(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Penyimpanan\PenerimaanProlisPenyimpanan::class, 'penerimaan_id');
    }
}