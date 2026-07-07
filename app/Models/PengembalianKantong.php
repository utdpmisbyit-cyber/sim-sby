<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengembalianKantong extends Model
{
    use SoftDeletes;

    protected $table      = 'pengembalian_kantong';
    protected $primaryKey = 'id';
    public    $incrementing = true;
    protected $keyType    = 'int';

    protected $fillable = [
        'no_kembali', 'tgl_kembali', 'no_kantong', 'stok_kantong_id','asal_darah_id', 
        'merk', 'jenis', 'tipe', 'ukuran', 'kondisi', 'keterangan', 'created_by',
    ];

    protected $casts = [
        'tgl_kembali' => 'date',
    ];

    public function stokKantong()
    {
        return $this->belongsTo(StokKantong::class, 'stok_kantong_id');
    }

    public function details()
    {
        return $this->hasMany(PengembalianKantongDetail::class, 'pengembalian_kantong_id');
    }
    public function asalDarah()
    {
        return $this->belongsTo(AsalDarah::class, 'asal_darah_id');
    }
}