<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemberianDarahDetail extends Model
{
    protected $table = 'pemberian_darah_detail';

    protected $fillable = [
        'pemberian_darah_id',
        'stok_darah_id',
        'no_stok',
        'jns_darah',
        'gol',
        'rhesus',
        'tgl_expired',
        'metode',
        'hasil',
        'keterangan',
        'jumlah',
        'cc',
        'harga_satuan',
        'total_harga',
    ];

    protected $casts = [
        'tgl_expired'  => 'date',
        'harga_satuan' => 'decimal:2',
        'total_harga'  => 'decimal:2',
    ];

   
    public function pemberianDarah(): BelongsTo
    {
        return $this->belongsTo(PemberianDarah::class, 'pemberian_darah_id');
    }

    public function stokDarah(): BelongsTo
    {
        return $this->belongsTo(StokDarah::class, 'stok_darah_id');
    }
}