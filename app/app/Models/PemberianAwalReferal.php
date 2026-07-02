<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PemberianAwalReferal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pemberian_awal_referal';

    protected $fillable = [
        'no_pemberian', 'fpup_id', 'no_fpup', 'tgl_fpup', 'nofpup_dari_cm',
        'cara_bayar', 'identifikasi_antibodi',
        'pasien_id', 'nama_pasien', 'noktp_pasien', 'jenis_kelamin', 'alamat_pasien',
        'kode_rs', 'nama_rs', 'no_reg',
        'gol_darah', 'rhesus', 'pasien_karier', 'seleksi',
        'stocks', 'biaya_lain', 'jumlah_kantong_per_seleksi', 'total_biaya',
        'status', 'catatan', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'tgl_fpup' => 'datetime',
        'identifikasi_antibodi' => 'boolean',
        'pasien_karier' => 'boolean',
        'stocks' => 'array',
        'biaya_lain' => 'array',
        'total_biaya' => 'decimal:2',
    ];

    protected $attributes = [
        'stocks' => '[]',
        'biaya_lain' => '[]',
    ];

    /**
     * Sesuaikan relasi ini dengan model FPUP yang sudah ada pada project Anda.
     * Contoh: App\Models\Referal\PermintaanFpupReferal
     */
    public function fpup()
    {
        return $this->belongsTo(\App\Models\PermintaanFpupReferal::class, 'fpup_id');
    }

    public function scopeCari($query, ?string $keyword)
    {
        if (! $keyword) {
            return $query;
        }

        return $query->where(function ($q) use ($keyword) {
            $q->where('no_pemberian', 'like', "%{$keyword}%")
                ->orWhere('no_fpup', 'like', "%{$keyword}%")
                ->orWhere('nama_pasien', 'like', "%{$keyword}%")
                ->orWhere('noktp_pasien', 'like', "%{$keyword}%");
        });
    }
}