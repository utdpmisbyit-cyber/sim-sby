<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengirimanDarahExternal extends Model
{
    use HasFactory;

    protected $table = 'pengiriman_darah_external';

    protected $fillable = [
        'nomor_pengiriman',
        'no_permintaan',
        'permintaan_id',
        'tanggal_kirim',
        'petugas',
        'petugas_kode',
        'penerima',
        'institusi_tujuan',
        'jenis_biaya',
        'dropping',
        'status',
        'suhu_kirim',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_kirim' => 'datetime',
    ];

    /**
     * Relasi ke permintaan darah external
     */
    public function permintaan()
    {
        return $this->belongsTo(PermintaanDarahExternal::class, 'permintaan_id');
    }

    /**
     * Relasi ke detail pengiriman
     */
    public function details()
    {
        return $this->hasMany(PengirimanDarahExternalDetail::class, 'pengiriman_id');
    }
}
