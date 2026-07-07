<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengambilanDarahSiklus extends Model
{
    protected $table = 'apheresis_pengambilan_darah_siklus';

    protected $fillable = [
        'pengambilan_darah_id',
        'siklus_ke',
        'jam',
        'draw_return_ml',
        'draw_return_menit',
        'plasma_vol',
        'platelet_yield',
        'plasma_vol_2',
        'nacl_sitrat',
        'keterangan',
    ];

    public function pengambilanDarah()
    {
        return $this->belongsTo(PengambilanDarahApheresis::class, 'pengambilan_darah_id');
    }
}