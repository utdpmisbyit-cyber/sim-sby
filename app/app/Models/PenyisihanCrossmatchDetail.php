<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenyisihanCrossmatchDetail extends Model
{
    protected $table = 'penyisihan_crossmatch_details';

    protected $fillable = [
        'penyisihan_crossmatch_id',
        'cross_test_id',
        'no_stock',
        'jns_darah',
        'gol_rh_kantong',
        'gol',
        'rhesus',
        'tgl_aftap',
        'tgl_kadaluarsa',
        'status_kantong',
        'alasan',
        'keterangan',
    ];

    protected $casts = [
        'tgl_aftap'      => 'date',
        'tgl_kadaluarsa' => 'date',
    ];

    public function header()
    {
        return $this->belongsTo(PenyisihanCrossmatch::class, 'penyisihan_crossmatch_id');
    }

    public function crossTest()
    {
        // Sesuaikan namespace App\Models\Crossmatch\CrossTest jika model Anda
        // berada di lokasi/namespace yang berbeda.
        return $this->belongsTo(CrossTest::class, 'cross_test_id');
    }
}