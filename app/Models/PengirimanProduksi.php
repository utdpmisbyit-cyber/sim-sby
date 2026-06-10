<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengirimanProduksi extends Model
{
    use SoftDeletes;

    protected $table = 'pengiriman_produksi';

    protected $fillable = ['kode', 'pengirim_id', 'penerima_id', 'dokumen'];

    public function pengirim()
    {
        return $this->belongsTo(Petugas::class, 'pengirim_id');
    }

    public function penerima()
    {
        return $this->belongsTo(Petugas::class, 'penerima_id');
    }

    public function produksiDarah()
    {
        return $this->hasMany(ProduksiDarah::class);
    }
}
