<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProduksiDarah extends Model
{
    const STATUS = ['SENDING', 'QUEUED', 'ONGOING', 'COMPLETED', 'VERIFIED'];

    protected $table = 'produksi_darah';

    protected $fillable = ['kode', 'barcode', 'status', 'pengiriman_produksi_id', 'gram', 'volume'];

    public function pengirimanProduksi()
    {
        return $this->belongsTo(PengirimanProduksi::class);
    }

    public function kantongDarahHasilProduksi()
    {
        return $this->belongsTo(KantongDarahHasilProduksi::class, 'barcode', 'kode');
    }

    /**
     * Get the RencanaProduksiDetail where barcode = no_kantong + no_satelit.
     * This is looked up dynamically, not a standard Eloquent relationship.
     */
    public static function findRencanaProduksiDetail(string $barcode): ?RencanaProduksiDetail
    {
        return RencanaProduksiDetail::whereRaw("CONCAT(no_kantong, no_satelit) = ?", [$barcode])->first();
    }
}
