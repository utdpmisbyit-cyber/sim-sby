<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fpup extends Model
{
    use SoftDeletes;

    protected $table = 'fpup';

    protected $fillable = [
        'nama_pasien',
        'no_ktp',
        'tgl_lahir',
        'umur',
        'jenis_kelamin',
        'kebangsaan',
        'no_telp',
        'alamat',
        'keluarga',
        'nama_dokter',
        'nama_instansi',
        'foto_ktp_path',
        'ocr_raw_result',
        'ocr_terverifikasi',
        'ocr_verified_at',
        'ocr_verified_by',
        'keterangan',
    ];

    protected $casts = [
        'tgl_lahir'          => 'date',
        'umur'               => 'integer',
        'keluarga'           => 'array',
        'ocr_raw_result'     => 'array',
        'ocr_terverifikasi'  => 'boolean',
        'ocr_verified_at'    => 'datetime',
    ];

    public function permintaanFpup(): HasMany
    {
        return $this->hasMany(PermintaanFpup::class, 'fpup_id');
    }

    public function permintaanFpupReferal(): HasMany
    {
        return $this->hasMany(PermintaanFpupReferal::class, 'fpup_id');
    }

    /**
     * Hitung umur otomatis dari tanggal lahir (dipakai sebagai fallback bila kolom umur kosong).
     */
    public function getUmurDihitungAttribute(): ?int
    {
        return $this->tgl_lahir?->age;
    }
}