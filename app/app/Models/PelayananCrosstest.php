<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PelayananCrosstest extends Model
{
    use SoftDeletes;

    protected $table = 'pelayanan_crosstest';

    protected $fillable = [
        'cross_test_id',
        'permintaan_fpup_id',
        'no_fpup',
        'no_stock',
        'jns_darah',
        'gol',
        'rhesus',
        'metode',
        'hasil',
        'nat',
        'skrining',
        'keterangan',
        'catatan',
        'pemeriksa',
        'tgl_periksa',
        'status',
    ];

    protected $casts = [
        'nat'        => 'boolean',
        'tgl_periksa' => 'datetime',
    ];

    public function crossTest(): BelongsTo
    {
        return $this->belongsTo(CrossTest::class, 'cross_test_id');
    }

    public function permintaanFpup(): BelongsTo
    {
        return $this->belongsTo(PermintaanFpup::class, 'permintaan_fpup_id');
    }

    public function getHasilBadgeAttribute(): string
    {
        return match ($this->hasil) {
            'Cocok'       => 'success',
            'Tidak Cocok' => 'danger',
            'Doubtful'    => 'warning',
            default       => 'secondary',
        };
    }

    public function getSkrningBadgeAttribute(): string
    {
        return match ($this->skrining) {
            'NEG' => 'success',
            'POS' => 'danger',
            default => 'secondary',
        };
    }
}