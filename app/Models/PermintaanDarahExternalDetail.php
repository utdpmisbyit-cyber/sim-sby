<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermintaanDarahExternalDetail extends Model
{
    protected $table = 'permintaan_darah_external_detail';
    
    protected $fillable = [
        'permintaan_id',
        'jenis_darah',
        'gol_darah',
        'rhesus',
        'donor_pengganti',    // perbaiki dari 'donor_penganti' menjadi 'donor_pengganti'
        'no_fpup',
        'fpup_id',
        'jumlah',
        'jumlah_dipenuhi',
        'keterangan',
        'tanggal_perlu'
    ];

    protected $casts = [
        'tanggal_perlu' => 'date',
    ];

    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(PermintaanDarahExternal::class, 'permintaan_id');
    }

    public function getSisaAttribute()
    {
        return $this->jumlah - $this->jumlah_dipenuhi;
    }

    public function getStatusPemenuhanAttribute()
    {
        if ($this->jumlah_dipenuhi >= $this->jumlah) {
            return 'Lengkap';
        } elseif ($this->jumlah_dipenuhi > 0) {
            return 'Sebagian';
        }
        return 'Belum';
    }
}