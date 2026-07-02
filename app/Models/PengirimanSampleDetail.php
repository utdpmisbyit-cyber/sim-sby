<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengirimanSampleDetail extends Model
{
    protected $table = 'pengiriman_sample_detail';

    protected $fillable = [
        'pengiriman_sample_id',
        'urut',
        'no_kantong',
        'jenis_kantong',
        'aftap_id',
        'tanggal_aftap',
        'donor_id',
        'no_donor',
        'nama_donor',
        'asal_darah_id',
        'kode_asal_darah',
        'gol_darah',
        'rhesus',
        'tolak',
        'keterangan',
        'status',
        'petugas_id',
        'cabang_id',
        'perkiraan',
        'jenis_donor',
        'suhu',
        'suhu_sample', 
        'id_logger',
        'id_coolbox',
    ];
   
    public $timestamps = true;
    protected $casts = [
        'tanggal_aftap' => 'datetime',
        'tolak'         => 'boolean',
    ];

    public function header()
    {
        return $this->belongsTo(PengirimanSample::class, 'pengiriman_sample_id');
    }
     public function aftap()
    {
        return $this->belongsTo(Aftap::class, 'aftap_id');

    }
     public function pengirimanSample(): BelongsTo
    {
        return $this->belongsTo(PengirimanSample::class, 'pengiriman_sample_id');
    }
    
    /**
     * Relasi ke Donor
     */
    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class, 'donor_id');
    }
    
    /**
     * Relasi ke AsalDarah
     */
    public function asalDarah(): BelongsTo
    {
        return $this->belongsTo(AsalDarah::class, 'asal_darah_id');
    }
}