<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokKantongPenerimaanDetail extends Model
{
    use HasFactory;

    protected $table = 'stok_kantong_penerimaan_detail';

    protected $guarded = ['id'];

    const STATUS_TERSEDIA   = 'tersedia';
    const STATUS_SAMPLE     = 'sample';
    const STATUS_SEROLOGI   = 'serologi';
    const STATUS_DISISIHKAN = 'disisihkan';

    public function penerimaan()
    {
        return $this->belongsTo(StokKantongPenerimaan::class, 'penerimaan_id');
    }

    public function penyisihanDetail()
    {
        return $this->belongsTo(PenyisihanKantongAftapDetail::class, 'penyisihan_detail_id');
    }

    public function scopeTersedia($query)
    {
        return $query->where('status_kirim', self::STATUS_TERSEDIA);
    }
}