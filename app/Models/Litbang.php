<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Litbang extends Model
{
    use SoftDeletes;

    protected $table = 'litbang';

    protected $fillable = [
        'no_kantong',
        'aftap_id',
        'donor_id',
        'status',
        'tanggal_kirim',
        'tanggal_konfirmasi',
        'golongan_darah',
        'rhesus',
        'petugas_kirim_id',
        'petugas_konfirmasi_id',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_kirim' => 'date',
        'tanggal_konfirmasi' => 'date',
    ];

    public function aftap()
    {
        return $this->belongsTo(Aftap::class, 'aftap_id');
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class, 'donor_id');
    }

    public function petugasKirim()
    {
        return $this->belongsTo(Petugas::class, 'petugas_kirim_id');
    }

    public function petugasKonfirmasi()
    {
        return $this->belongsTo(Petugas::class, 'petugas_konfirmasi_id');
    }
}
