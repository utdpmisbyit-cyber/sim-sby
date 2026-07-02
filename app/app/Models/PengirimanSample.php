<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengirimanSample extends Model
{
    protected $table = 'pengiriman_sample';

    protected $fillable = [
        'no_fpd',
        'tanggal_fpd',
        'total',
        'stok',
        'keterangan',
        'type_kantong',
        'suhu',
        'is_nat',
        'petugas_pemeriksa',
        'id_logger',
        'id_coolbox',
    ];

    protected $casts = [
        'tanggal_fpd' => 'date',
        'is_nat'      => 'boolean',
    ];

    public function detail()
    {
        return $this->hasMany(PengirimanSampleDetail::class, 'pengiriman_sample_id');
    }
}