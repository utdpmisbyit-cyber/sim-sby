<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengirimanBankDarahInternal extends Model
{
    use SoftDeletes;

    protected $table =
        'pengiriman_bank_darah_internal';

    protected $guarded = [];

    protected $casts = [
        'tanggal_pengiriman' => 'datetime'
    ];

    public function details()
    {
        return $this->hasMany(
            PengirimanBankDarahInternalDetail::class,
            'pengiriman_bank_darah_internal_id'
        );
    }

    public function permintaan()
    {
        return $this->belongsTo(
            PermintaanDarahPenyimpanan::class,
            'permintaan_darah_penyimpanan_id'
        );
    }
}