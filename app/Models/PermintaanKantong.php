<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanKantong extends Model
{ 
    protected $table = 'permintaan_kantong';
    protected $primaryKey = 'id';
    public    $incrementing = true;
    protected $keyType    = 'int';
    public $timestamps = false; 

    protected $fillable = [
        'bagian_petugas_id',
        'petugas_id',
        'verifikator_id',
        'keterangan',
        'nomor',
        'status',  
        'tanggal',
        'flag',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'flag' => 'integer',
    ];

    public function bagianPetugas()
    {
        return $this->belongsTo(BagianPetugas::class, 'bagian_petugas_id');
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'petugas_id');
    }

    public function verifikator()
    {
        return $this->belongsTo(Petugas::class, 'verifikator_id');
    }

    public function details()
    {
        return $this->hasMany(PermintaanKantongDetail::class, 'permintaan_kantong_id');
    }
 
    /** Semua record pengeluaran yang terkait permintaan ini */
    public function pengeluaran()
    {
        return $this->hasMany(PengeluaranKantong::class, 'permintaan_kantong_id', 'id');
    }
 
   
    public function scopeBelumSelesai($query)
    {
        return $query->whereNotIn('status', ['SELESAI']);
    }
 
    /** Hanya permintaan yang sudah selesai */
    public function scopeSelesai($query)
    {
        return $query->where('status', 'SELESAI');
    }
}
