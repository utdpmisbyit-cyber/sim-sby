<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengembalianDarahCrossmatchDetail extends Model
{
    use HasFactory;

    protected $table = 'pengembalian_darah_crossmatch_detail';

    protected $fillable = [
        'pengembalian_id',
        'no_stock',
        'jenis_darah',
        'gol_darah',
        'rhesus',
        'sts',
        'status_kembali',
        'alasan_kembali',
        'tgl_aftap',
        'kadaluarsa',
        'jumlah',
        'keterangan',
    ];

    protected $casts = [
        'tgl_aftap'  => 'date',
        'kadaluarsa' => 'date',
        'jumlah'     => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function pengembalian()
    {
        return $this->belongsTo(PengembalianDarahCrossmatch::class, 'pengembalian_id');
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status_kembali) {
            'Baik'       => 'badge-success',
            'Rusak'      => 'badge-danger',
            'Kadaluarsa' => 'badge-warning',
            default      => 'badge-secondary',
        };
    }

    public function getKadaluarsaFormattedAttribute(): string
    {
        return $this->kadaluarsa ? $this->kadaluarsa->format('d/m/Y') : '-';
    }

    public function getTglAftapFormattedAttribute(): string
    {
        return $this->tgl_aftap ? $this->tgl_aftap->format('d/m/Y') : '-';
    }
}