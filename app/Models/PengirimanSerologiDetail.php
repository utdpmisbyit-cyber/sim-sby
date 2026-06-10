<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengirimanSerologiDetail extends Model
{
    protected $table = 'pengiriman_serologi_detail';

    protected $fillable = [
        'pengiriman_serologi_id',
        'no_kantong',
        'no_selang',
        'jenis_kantong',
        'no_donor',
        'nama_donor',
        'gol_darah',
        'rhesus',
        'asal_darah',
        'tanggal_aftap',
        'tolak',
        'is_nat',
    ];

    public function pengiriman()
    {
        return $this->belongsTo(
            PengirimanSerologi::class,
            'pengiriman_serologi_id'
        );
    }
}