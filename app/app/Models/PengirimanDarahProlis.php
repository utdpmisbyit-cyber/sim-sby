<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Petugas;
use App\Models\Donor;

class PengirimanDarahProlis extends Model
{
    use SoftDeletes;

    protected $table = 'pengiriman_darah_prolis';

    protected $fillable = [
        'no_pengiriman',
        'tgl_pengiriman',
        'no_stok',
        'no_kantong',
        'jenis_darah',
        'golongan_darah',
        'rhesus',
        'tgl_aftap',
        'tgl_produksi',
        'tgl_expired',
        'nama_asal_darah',
        'status',
        'gr',
        'ml',
        'jumlah',
        'skrining',
        'keterangan',
        'no_fpd',
        'asal_darah_id',
        'petugas_id',
        'suhu',
        'created_by'
    ];

    protected $casts = [
        'tgl_pengiriman' => 'datetime',
        'tgl_aftap' => 'datetime',
        'tgl_produksi' => 'datetime',
        'tgl_expired' => 'datetime',
        'suhu'        => 'float',
        'deleted_at' => 'datetime'
    ];

    // RELATION
    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'petugas_id');
    }

    public function creator()
    {
        return $this->belongsTo(Petugas::class, 'created_by');
    }

    public function asalDarah()
    {
        return $this->belongsTo(Donor::class, 'asal_darah_id');
    }

    // ATTRIBUTE
    public function getIsExpiredAttribute()
    {
        return $this->tgl_expired
            ? $this->tgl_expired->isPast()
            : false;
    }

    // SCOPE
    public function scopeSearch($query, $keyword)
    {
        if (!$keyword) {
            return $query;
        }

        return $query->where(function ($q) use ($keyword) {
            $q->where('no_pengiriman', 'like', "%{$keyword}%")
            ->orWhere('no_kantong', 'like', "%{$keyword}%")
            ->orWhere('no_stok', 'like', "%{$keyword}%")
            ->orWhere('nama_asal_darah', 'like', "%{$keyword}%");
        });
    }

    public function scopeFilterJenis($query, $jenis)
    {
        if (!$jenis) {
            return $query;
        }

        return $query->where('jenis_darah', $jenis);
    }

    public function scopeFilterGolongan($query, $golongan)
    {
        if (!$golongan) {
            return $query;
        }

        return $query->where('golongan_darah', $golongan);
    }

    public function scopeFilterTglPengiriman($query, $dari, $sampai)
    {
        return $query
            ->when($dari, function ($q) use ($dari) {
                $q->whereDate('tgl_pengiriman', '>=', $dari);
            })
            ->when($sampai, function ($q) use ($sampai) {
                $q->whereDate('tgl_pengiriman', '<=', $sampai);
            });
    }
}