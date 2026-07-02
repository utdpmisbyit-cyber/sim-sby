<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermintaanDarahPenyimpananDetail extends Model
{
    protected $table = 'permintaan_darah_penyimpanan_detail';

    protected $fillable = [
        'permintaan_darah_penyimpanan_id',
        'jenis_darah',
        'golongan_darah',
        'rhesus',
        'jumlah_kantong',
        'jumlah_cc',
        'tanggal_perlu',
        'no_fpup',
        'nama_os',
        'status',
        'keterangan',
    ];

    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(
            PermintaanDarahPenyimpanan::class,
            'permintaan_darah_penyimpanan_id'
        );
    }
}