<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PendataanKantong extends Model
{   
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
    const TYPE_KANTONG = [
        'AP', 'AP2', 'AP3', 'APc','APk','APp',
        'DB','DJ',
        'DK',
        'PD',
        'QR','QD','QW',
        'QT',
        'SG',
        'TR','TJ'
    ];
    const JENIS_TYPE_MAP = [
        'Apheresis'    => ['AP','AP2','AP3','APc','APk','APp'],
        'Double Besar' => ['DB'],
        'Double Jumbo' => ['DJ'],
        'Double Kecil' => ['DK'],
        'Pediatrix'    => ['PD'],
        'Quadruple'    => ['QR','QD','QW','QT'],
        'Single'       => ['SG'],
        'Triple'       => ['TR','TJ'],
    ];
    const UKURAN = ['350 CC', '250 CC', '450 CC', '550 CC'];

    use SoftDeletes;

    protected $table = 'pendataan_kantong';

    protected $fillable = ['kode', 'status', 'barcode', 'pengiriman_serologi_id'];

    public function pengirimanSerologi()
    {
        return $this->belongsTo(PengirimanSerologi::class);
    }

    public function kantongDarahHasilProduksi()
    {
        return $this->belongsTo(KantongDarahHasilProduksi::class, 'barcode', 'kode');
    }
    public function scopeSudahDigenerate($query)
    {
        return $query->whereNotNull('barcode');
    }
}
