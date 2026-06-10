<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProduksiDarah extends Model
{
    const STATUS = ['SENDING', 'QUEUED', 'ONGOING', 'COMPLETED'];

    use SoftDeletes;

    protected $table = 'produksi_darah';

    protected $fillable = ['kode', 'barcode', 'status', 'pengiriman_produksi_id'];

    public function pengirimanProduksi()
    {
        return $this->belongsTo(PengirimanProduksi::class);
    }

    public function kantongDarahHasilProduksi()
    {
        return $this->belongsTo(KantongDarahHasilProduksi::class, 'barcode', 'kode');
    }
}
