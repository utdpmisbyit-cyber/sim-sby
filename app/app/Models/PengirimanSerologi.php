<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengirimanSerologi extends Model
{
    use SoftDeletes;

    protected $table = 'pengiriman_serologi';

    protected $fillable = ['kode', 'pengirim_id', 'penerima_id', 'dokumen'];
    

    public function pengirim()
    {
        return $this->belongsTo(Petugas::class, 'pengirim_id');
    }

    public function penerima()
    {
        return $this->belongsTo(Petugas::class, 'penerima_id');
    }

    public function pemeriksaanSampel()
    {
        return $this->hasMany(PemeriksaanSampel::class);
    }
}
