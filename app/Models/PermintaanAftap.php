<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermintaanAftap extends Model
{
    use SoftDeletes;
    const STATUS = ['PENDING', 'ACCEPTED', 'REJECTED'];
    const MERK_KANTONG = [
        'Amicore',
        'Compoflex', 'Global', 'Green Cross',
        'Hemonetic','HL Haemopack','iControl',
        'JMS','Karmi','Kawasumi',
        'Lain-lain','Mitra','Nigale',
        'Oneject One Bag','Terumo',
        'Terumo one Bag','Trima','Wego','Zontic',
    ];
    const JENIS_KANTONG = [
        'Apheresis','Double Besar','Double Jumbo','Double Kecil',
        'Pediatrix','Quadruple','Single','Triple',
    ];
    const UKURAN = ['350 CC', '250 CC', '450 CC', '550 CC'];
    protected $table = 'permintaan_aftap';
    protected $fillable = ['kode','merk','jenis','ukuran','jumlah','status',];

    public function kantongDarah()
    {
        return $this->belongsToMany(KantongDarah::class, 'kantong_darah_permintaan_aftap', 'permintaan_aftap_id', 'kantong_darah_id')
                    ->withPivot('jumlah');
    }
    public function items(){return $this->hasMany(PermintaanAftapItem::class, 'permintaan_aftap_id');}
    
}
