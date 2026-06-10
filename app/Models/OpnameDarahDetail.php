<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpnameDarahDetail extends Model
{
    protected $table = 'opname_darah_detail';

    protected $fillable = [
        'opname_darah_id',
        'no_stok',
        'stok_darah_id',
        'jenis_darah',
        'golongan_darah',
        'rhesus',
        'tgl_kadaluarsa',
        'jumlah_sistem',
        'jumlah_fisik',
        'selisih',
        'keterangan',
    ];

    protected $casts = [
        'tgl_kadaluarsa' => 'date',
    ];

    
    public function opname(): BelongsTo
    {
        return $this->belongsTo(OpnameDarah::class, 'opname_darah_id');
    }

    public function stokDarah(): BelongsTo
    {
        return $this->belongsTo(StokDarah::class, 'stok_darah_id');
    }

    
    public function getGolRhAttribute(): string
    {
        return trim(($this->golongan_darah ?? '') . ' ' . ($this->rhesus ?? ''));
    }

    public function getStatusKadaluarsaAttribute(): string
    {
        if (!$this->tgl_kadaluarsa) return 'unknown';
        return $this->tgl_kadaluarsa->isPast() ? 'expired' : 'valid';
    }
}