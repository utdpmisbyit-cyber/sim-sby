<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RencanaProduksi extends Model
{
    protected $table = 'rencana_produksi';
    protected $fillable = [
        'pengiriman_sample_id',
        'tanggal',
        'petugas_id',
        'tipe_kantong_id',
    ];

    public function pengirimanSample()
    {
        return $this->belongsTo(PengirimanSample::class, 'pengiriman_sample_id');
    }

    public function tipeKantong()
    {
        return $this->belongsTo(TipeKantong::class, 'tipe_kantong_id');
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'petugas_id');
    }

    public function details()
    {
        return $this->hasMany(RencanaProduksiDetail::class, 'rencana_produksi_id');
    }
}
