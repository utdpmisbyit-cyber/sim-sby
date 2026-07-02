<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengembalianDarahReferal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pengembalian_darah_referal';

    protected $fillable = [
        'nomor_kembali',
        'tanggal_kembali',
        'kode_petugas',
        'nama_petugas',
        'no_fpup',
        'tgl_fpup',
        'no_stock',
        'kode_rumah_sakit',
        'nama_rumah_sakit',
        'alasan_kembali',
        'status_kembali',
        'tgl_pemberian',
        'umur_hari_pemberian',
        'yang_mengembalikan',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_kembali'     => 'date',
        'tgl_fpup'            => 'date',
        'tgl_pemberian'       => 'date',
        'umur_hari_pemberian' => 'integer',
    ];

    protected $dates = ['deleted_at'];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function details()
    {
        return $this->hasMany(PengembalianDarahReferalDetail::class, 'pengembalian_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeSearch($query, string $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('nomor_kembali',   'like', "%{$keyword}%")
              ->orWhere('no_fpup',        'like', "%{$keyword}%")
              ->orWhere('nama_rumah_sakit','like', "%{$keyword}%")
              ->orWhere('nama_petugas',   'like', "%{$keyword}%");
        });
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getTanggalKembaliFormattedAttribute(): string
    {
        return $this->tanggal_kembali
            ? $this->tanggal_kembali->format('d/m/Y')
            : '-';
    }

    public function getTglFpupFormattedAttribute(): string
    {
        return $this->tgl_fpup
            ? $this->tgl_fpup->format('d/m/Y')
            : '-';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status_kembali) {
            'Baik'       => 'badge-success',
            'Rusak'      => 'badge-danger',
            'Kadaluarsa' => 'badge-warning',
            default      => 'badge-secondary',
        };
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Generate nomor pengembalian otomatis: -YYYYMM-XXXX
     */
    public static function generateNomor(): string
    {
        $prefix = 'RD-' . now()->format('Ym') . '-';
        $last   = static::withTrashed()
                         ->where('nomor_kembali', 'like', $prefix . '%')
                         ->orderByDesc('nomor_kembali')
                         ->value('nomor_kembali');

        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}