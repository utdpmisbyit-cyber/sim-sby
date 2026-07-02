<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KantongDarahHasilProduksi extends Model
{
    const PEMILIK = ['Gudang', 'Aftap'];

    use SoftDeletes;

    protected $table = 'kantong_darah_hasil_produksi';

    protected $fillable = [
        'kode', 'kantong_darah_id', 'log_donor_id', 'produksi_kantong_darah_id', 'pemilik',
    ];

    public function kantongDarah()
    {
        return $this->belongsTo(KantongDarah::class);
    }

    public function logDonor()
    {
        return $this->belongsTo(LogDonor::class);
    }

    public function produksiKantongDarah()
    {
        return $this->belongsTo(ProduksiKantongDarah::class);
    }

    public function pemeriksaanSampel()
    {
        return $this->hasOne(PemeriksaanSampel::class, 'barcode', 'kode');
    }

    public function produksiDarah()
    {
        return $this->hasOne(ProduksiDarah::class, 'barcode', 'kode');
    }

    public function plateMappingSerologi()
    {
        return $this->hasMany(PlateMappingSerologi::class, 'barcode', 'kode');
    }
}
