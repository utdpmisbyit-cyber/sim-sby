<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermintaanKantongDetail extends Model
{
    use SoftDeletes;
    
    protected $table = 'permintaan_kantong_detail';
    protected $primaryKey = 'id';
    public    $incrementing = true;
    protected $keyType    = 'int';

    protected $fillable = [
        'permintaan_kantong_id',
        'kode',
        'merk',
        'jenis',
        'ukuran',
        'jumlah',
        'status',
        'flag',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'flag' => 'integer',
    ];

    public function permintaanKantong()
    {
        return $this->belongsTo(PermintaanKantong::class, 'permintaan_kantong_id','id');
    }
     public function pengeluaran()
    {
        return $this->hasMany(PengeluaranKantong::class, 'detail_id', 'id');
    }
    public function getSisaAttribute(): int
    {
        return max(0, $this->jumlah - $this->jumlah_dilayani);
    }
 
    public function scopeBelumSelesai($query)
    {
        return $query->whereNotIn('status', ['SELESAI']);
    }
    public function tipeKantong()
    {
        return $this->belongsTo(TipeKantong::class, 'tipe_kantong_id');
    }
}
