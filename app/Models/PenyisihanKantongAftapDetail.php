<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenyisihanKantongAftapDetail extends Model
{
    use HasFactory;

    protected $table = 'penyisihan_kantong_aftap_detail';

    protected $guarded = ['id'];

    public function penyisihan()
    {
        return $this->belongsTo(PenyisihanKantongAftap::class, 'penyisihan_id');
    }

    public function penerimaanDetail()
    {
        return $this->belongsTo(StokKantongPenerimaanDetail::class, 'penerimaan_detail_id');
    }
}