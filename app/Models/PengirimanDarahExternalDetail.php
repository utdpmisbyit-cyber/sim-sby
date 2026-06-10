<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengirimanDarahExternalDetail extends Model
{
    use HasFactory;

    protected $table = 'pengiriman_darah_external_detail';

    protected $fillable = [
        'pengiriman_id',
        'permintaan_detail_id',
        'no_stock',
        'jenis_darah',
        'gol_darah',
        'rhesus',
        'jumlah',
        'tgl_kadaluarsa',
        'nat',
        'keterangan',
    ];

    protected $casts = [
        'tgl_kadaluarsa' => 'datetime',
        'nat'            => 'boolean',
    ];

    /**
     * Relasi ke pengiriman header
     */
    public function pengiriman()
    {
        return $this->belongsTo(PengirimanDarahExternal::class, 'pengiriman_id');
    }

    /**
     * Relasi ke detail permintaan
     */
    public function permintaanDetail()
    {
        return $this->belongsTo(PermintaanDarahExternalDetail::class, 'permintaan_detail_id');
    }
}
