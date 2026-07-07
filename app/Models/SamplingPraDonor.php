<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SamplingPraDonor extends Model
{
    use HasFactory;

    protected $table = 'apheresis_sampling_pra_donors';

    protected $fillable = [
        'no_transaksi',
        'server_date',
        'petugas_id',
        'pendaftaran_id',
        'no_donor',
        'nama_donor',
        'tgl_lahir',
        'rhesus',
        'jenis_kelamin',
        'golongan_darah',
        'wbc', 'neut', 'lymph', 'mono', 'eo', 'baso', 'ig',
        'rbc', 'hgb', 'hct', 'mcv', 'mch', 'mchc', 'rdw_sd', 'rdw_cv',
        'plt', 'pdw', 'mpv', 'p_lcr', 'pct',
        'status_lulus',
        'alasan_tidak_lulus',
        'keterangan',
    ];

    protected $casts = [
        'server_date' => 'datetime',
        'tgl_lahir' => 'date',
        'alasan_tidak_lulus' => 'array',
        'wbc' => 'float', 'neut' => 'float', 'lymph' => 'float', 'mono' => 'float',
        'eo' => 'float', 'baso' => 'float', 'ig' => 'float',
        'rbc' => 'float', 'hgb' => 'float', 'hct' => 'float', 'mcv' => 'float',
        'mch' => 'float', 'mchc' => 'float', 'rdw_sd' => 'float', 'rdw_cv' => 'float',
        'plt' => 'float', 'pdw' => 'float', 'mpv' => 'float', 'p_lcr' => 'float', 'pct' => 'float',
    ];

    /** Daftar alasan tidak lulus yang tersedia di UI (dipakai controller & view) */
    public const ALASAN_OPTIONS = [
        'lipemik'     => 'Lipemik',
        'hb_tinggi'   => 'HB Tinggi',
        'hb_rendah'   => 'HB Rendah',
        'plt_tinggi'  => 'PLT Tinggi',
        'plt_rendah'  => 'PLT Rendah',
        'rbc_tinggi'  => 'RBC Tinggi',
        'wbc_tinggi'  => 'WBC Tinggi',
        'lain_lain'   => 'Lain-lain',
    ];

    public function petugas()
    {
        return $this->belongsTo(\App\Models\User::class, 'petugas_id');
    }

    public function pendaftaran()
    {
        return $this->belongsTo(\App\Models\Apheresis\Pendaftaran::class, 'pendaftaran_id');
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('no_transaksi', 'like', "%{$term}%")
              ->orWhere('no_donor', 'like', "%{$term}%")
              ->orWhere('nama_donor', 'like', "%{$term}%");
        });
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_lulus) {
            'lulus' => 'Lulus',
            'tidak_lulus' => 'Tidak Lulus',
            default => '-',
        };
    }
}