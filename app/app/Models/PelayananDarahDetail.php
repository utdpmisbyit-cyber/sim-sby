<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PelayananDarahDetail extends Model
{
    protected $table = 'pelayanan_darah_detail';

    protected $fillable = [
        'pelayanan_darah_id',
        'pemberian_darah_detail_id',
        'no_stok',
        'jns_darah',
        'gol',
        'rhesus',
        'jumlah',
        'cc',
        'harga_satuan',
        'total_harga',
        'keterangan',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'total_harga'  => 'decimal:2',
    ];

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function pelayananDarah(): BelongsTo
    {
        return $this->belongsTo(PelayananDarah::class, 'pelayanan_darah_id');
    }

    public function pemberianDarahDetail(): BelongsTo
    {
        return $this->belongsTo(PemberianDarahDetail::class, 'pemberian_darah_detail_id');
    }
}