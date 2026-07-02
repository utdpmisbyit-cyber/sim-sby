<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanFpupDetail extends Model
{
    protected $table   = 'permintaan_fpup_detail';
    protected $guarded = ['id'];

    protected $casts = [
        'tgl_perlu' => 'date',
        'jumlah'    => 'integer',
        'cc'        => 'integer',
    ];

    public function permintaan()
    {
        return $this->belongsTo(PermintaanFpup::class, 'permintaan_fpup_id');
    }

    public function getJnsDarahLabelAttribute(): string
    {
        return PermintaanFpup::JNS_DARAH[$this->jns_darah] ?? $this->jns_darah;
    }
}