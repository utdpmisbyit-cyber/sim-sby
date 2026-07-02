<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PenerimaanProlisPenyimpanan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penerimaan_prolis_penyimpanan';

    protected $fillable = [
        'no_penerimaan',
        'tgl_penerimaan',
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
        'ruang',
        'gr',
        'ml',
        'jumlah',
        'skrining',
        'keterangan',
        'no_fpd',
        'asal_darah_id',
        'petugas_id',
        'created_by',
    ];

    protected $casts = [
        'tgl_penerimaan' => 'date',
        'tgl_aftap'      => 'date',
        'tgl_produksi'   => 'date',
        'tgl_expired'    => 'date',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function asalDarah()
    {
        return $this->belongsTo(AsalDarah::class, 'asal_darah_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeByNoPenerimaan($query, $no)
    {
        return $query->where('no_penerimaan', $no);
    }

    public function scopeByGolongan($query, $gol)
    {
        return $query->where('golongan_darah', $gol);
    }

    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_darah', $jenis);
    }

    public function scopeByRhesus($query, $rhesus)
    {
        return $query->where('rhesus', $rhesus);
    }

    public function getTglPenerimaanFormattedAttribute(): string
    {
        return $this->tgl_penerimaan?->format('d-m-Y') ?? '-';
    }

    public function getTglAftapFormattedAttribute(): string
    {
        return $this->tgl_aftap?->format('d-m-Y H:i') ?? '-';
    }

    public function getTglProduksiFormattedAttribute(): string
    {
        return $this->tgl_produksi?->format('d-m-Y H:i') ?? '-';
    }

    public function getTglExpiredFormattedAttribute(): string
    {
        return $this->tgl_expired?->format('d-m-Y H:i') ?? '-';
    }

    // ─── Static Helpers ───────────────────────────────────────────────────────

    public static function generateNoPenerimaan(): string
    {
         return \Illuminate\Support\Facades\DB::transaction(function () {
            $prefix = 'D' . now()->format('Y');

            $last = static::where('no_penerimaan', 'like', $prefix . '%')
                        ->lockForUpdate()          // ← lock row terakhir
                        ->orderByDesc('id')
                        ->value('no_penerimaan');

            $seq = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

            return $prefix . str_pad($seq, 8, '0', STR_PAD_LEFT);
        });
    }
}