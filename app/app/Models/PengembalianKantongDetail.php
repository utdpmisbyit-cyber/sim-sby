<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengembalianKantongDetail extends Model
{
    protected $table = 'pengembalian_kantong_detail';

    protected $fillable = [
        'pengembalian_kantong_id',
        'tipe_kantong_id',
        'jumlah',
        'flag',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'flag' => 'integer',
    ];

    public function pengembalian_kantong()
    {
        return $this->belongsTo(PengembalianKantong::class, 'pengembalian_kantong_id');
    }

    public function tipe_kantong()
    {
        return $this->belongsTo(TipeKantong::class, 'tipe_kantong_id');
    }
}
