<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RencanaProduksiDetail extends Model
{
    protected $table = 'rencana_produksi_detail';

    protected $fillable = [
        'rencana_produksi_id',
        'no_kantong',
        'no_satelit',
        'jenis_darah',
        'gram',
        'volume',
    ];

    public function rencanaProduksi()
    {
        return $this->belongsTo(RencanaProduksi::class, 'rencana_produksi_id');
    }
}
