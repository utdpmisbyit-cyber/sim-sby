<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KantongDarahPermintaanAftap extends Model
{
    use SoftDeletes;

    protected $table = 'kantong_darah_permintaan_aftap';

    protected $fillable = [
        'permintaan_aftap_id', 'kantong_darah_id', 'jumlah'
    ];

    public function permintaanAftap()
    {
        return $this->belongsTo(PermintaanAftap::class);
    }

    public function kantongDarah()
    {
        return $this->belongsTo(KantongDarah::class);
    }
}
