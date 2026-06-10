<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PemeriksaanSampel extends Model
{
    const STATUS = ['SENDING', 'QUEUED', 'ONGOING', 'COMPLETED'];

    use SoftDeletes;

    protected $table = 'pemeriksaan_sampel';

    protected $fillable = ['kode', 'status', 'barcode', 'pengiriman_serologi_id'];

    public function pengirimanSerologi()
    {
        return $this->belongsTo(PengirimanSerologi::class);
    }

    public function kantongDarahHasilProduksi()
    {
        return $this->belongsTo(KantongDarahHasilProduksi::class, 'barcode', 'kode');
    }
}
