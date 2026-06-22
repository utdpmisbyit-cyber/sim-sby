<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermintaanFpupReferalDetail extends Model
{
    protected $table = 'permintaan_fpup_referal_detail';

    protected $fillable = [
        'permintaan_fpup_referal_id',
        'jns_darah',
        'gol_darah',
        'rhesus',
        'jumlah',
        'cc',
        'tgl_perlu',
        'keterangan',
    ];

    protected $casts = [
        'tgl_perlu' => 'date',
        'jumlah'    => 'integer',
        'cc'        => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function header(): BelongsTo
    {
        return $this->belongsTo(PermintaanFpupReferal::class, 'permintaan_fpup_referal_id');
    }
     public function jenisDarah(): BelongsTo
    {
        return $this->belongsTo(JenisDarah::class, 'jns_darah');
    }
}