<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProduksiKantongDarah extends Model
{
    use SoftDeletes;

    protected $table = 'produksi_kantong_darah';

    protected $fillable = ['kode', 'kantong_darah_id', 'nomor_lot'];

    public function kantongDarah()
    {
        return $this->belongsTo(KantongDarah::class);
    }

    public function hasilProduksi()
    {
        return $this->hasOne(KantongDarahHasilProduksi::class);
    }
}
