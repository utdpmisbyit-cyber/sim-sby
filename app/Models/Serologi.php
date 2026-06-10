<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Serologi extends Model
{
    protected $table = 'serologi';

    protected $fillable = [
        'nomor',
        'tanggal',
        'jenis_periksa_serologi_id',
        'metode_serologi_id',
        'reagen_serologi_id',
        'group',
        'petugas_id',
        'pemeriksa_serologi_id',
        'diputar_oleh_id',
        'diperiksa_oleh_id',
        'disahkan_oleh_id',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'petugas_id');
    }

    public function pemeriksaSerologi()
    {
        return $this->belongsTo(Petugas::class, 'pemeriksa_serologi_id');
    }

    public function jenisPeriksaSerologi()
    {
        return $this->belongsTo(JenisPeriksaSerologi::class, 'jenis_periksa_serologi_id');
    }

    public function metodeSerologi()
    {
        return $this->belongsTo(MetodeSerologi::class, 'metode_serologi_id');
    }

    public function reagenSerologi()
    {
        return $this->belongsTo(ReagenSerologi::class, 'reagen_serologi_id');
    }

    public function details()
    {
        return $this->hasMany(SerologiDetail::class, 'serologi_id');
    }

    public function diputarOleh()
    {
        return $this->belongsTo(Petugas::class, 'diputar_oleh_id');
    }

    public function diperiksaOleh()
    {
        return $this->belongsTo(Petugas::class, 'diperiksa_oleh_id');
    }

    public function disahkanOleh()
    {
        return $this->belongsTo(Petugas::class, 'disahkan_oleh_id');
    }
}
