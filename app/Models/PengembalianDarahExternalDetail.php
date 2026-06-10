<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengembalianDarahExternalDetail extends Model
{
    use HasFactory;

    protected $table = 'pengembalian_darah_external_detail';

    protected $fillable = [
        'pengembalian_id',
        'no_stok',
        'no_kantong',
        'no_donor',
        'stok_darah_id',
        'jenis_darah',
        'golongan_darah',
        'rhesus',
        'tgl_aftap',
        'tgl_expired',
        'status_stok',
        'status_kembali',
        'alasan_kembali',
        'jumlah',
        'keterangan',
    ];

    protected $casts = [
        'tgl_aftap'    => 'date',
        'tgl_expired'  => 'date',
        'jumlah'       => 'integer',
    ];

    /**
     * Header Pengembalian
     */
    public function pengembalian()
    {
        return $this->belongsTo(
            PengembalianDarahExternal::class,
            'pengembalian_id'
        );
    }

    /**
     * Relasi ke stok darah
     */
    public function stokDarah()
    {
        return $this->belongsTo(
            StokDarah::class,
            'stok_darah_id'
        );
    }
}