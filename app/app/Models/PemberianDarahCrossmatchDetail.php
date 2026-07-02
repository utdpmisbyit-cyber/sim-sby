<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemberianDarahCrossmatchDetail extends Model
{
    protected $table = 'pemberian_darah_crossmatch_detail';

    protected $fillable = [
        'pemberian_darah_id',
        'nostock',
        'jns_darah',
        'gol',
        'rh',
        'tgl_expired',
        'cc',
        'jumlah',
    ];

    protected $casts = [
        'tgl_expired' => 'date',
    ];

    /* ------------------------------------------------------------------ */
    /*  Relationships                                                       */
    /* ------------------------------------------------------------------ */

    public function pemberianDarah(): BelongsTo
    {
        return $this->belongsTo(PemberianDarahCrossmatch::class, 'pemberian_darah_id');
    }

    /* ------------------------------------------------------------------ */
    /*  Accessors                                                          */
    /* ------------------------------------------------------------------ */

    public function getTglExpiredFormatAttribute(): string
    {
        return $this->tgl_expired
            ? $this->tgl_expired->format('d-m-Y')
            : '-';
    }
}