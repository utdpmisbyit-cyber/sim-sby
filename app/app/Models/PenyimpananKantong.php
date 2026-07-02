<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenyimpananKantong extends Model
{
    protected $table = 'penyimpanan_kantong';
    protected $fillable = [
        'bagian_petugas_id',
        'tipe_kantong_id',
        'jumlah',
    ];

    public function bagianPetugas()
    {
        return $this->belongsTo(BagianPetugas::class, 'bagian_petugas_id');
    }

    public function tipeKantong()
    {
        return $this->belongsTo(TipeKantong::class, 'tipe_kantong_id');
    }
}
