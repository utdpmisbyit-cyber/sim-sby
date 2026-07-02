<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FraksionasiDarah extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fraksionasi_darah';

    protected $fillable = [
        'no_fraksionasi',
        'jenis_darah',
        'golongan_darah',
        'rhesus',
        'no_kantong',
        'ukuran_kantong',
        'jenis_kantong',
        'tipe_kantong',
        'merk',
        'no_transaksi',
        'no_stok',
        'suhu_box',
        'tgl_dropping',
        'tgl_produksi',
        'tgl_kadaluarsa',
        'nomor_rak',
        'nomor_box',
        'status',
        'keterangan',
        'pendataan_kantong_id',
        'stok_darah_id',
        'petugas_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tgl_dropping'   => 'datetime',
        'tgl_produksi'   => 'datetime',
        'tgl_kadaluarsa' => 'datetime',
    ];

    // ─── Relationships ──────────────────────────────────────────────────────────

    public function pendataanKantong()
    {
        return $this->belongsTo(PendataanKantong::class, 'pendataan_kantong_id');
    }

    public function stokDarah()
    {
        return $this->belongsTo(StokDarah::class, 'stok_darah_id');
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'petugas_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Petugas::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(Petugas::class, 'updated_by');
    }

    // ─── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeProses($query)
    {
        return $query->where('status', 'proses');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'proses'  => 'warning',
            'selesai' => 'success',
            'batal'   => 'danger',
            default   => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'proses'  => 'Proses',
            'selesai' => 'Selesai',
            'batal'   => 'Batal',
            default   => '-',
        };
    }
}