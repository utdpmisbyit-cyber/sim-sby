<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermintaanDarahPenyimpanan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'permintaan_darah_penyimpanan';

    protected $fillable = [
        'no_permintaan',
        'bank_darah_kode',
        'bank_darah_nama',
        'petugas_kode',
        'petugas_nama',
        'tipe',
        'tanggal_minta',
        'status',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_minta' => 'date',
    ];

    // ─── Status Constants ──────────────────────────────────────────────────────
    const STATUS_PERMINTAAN = 'permintaan';
    const STATUS_PROSES     = 'proses';
    const STATUS_SELESAI    = 'selesai';
    const STATUS_BATAL      = 'batal';

    public static function statusLabel(): array
    {
        return [
            self::STATUS_PERMINTAAN => 'Permintaan',
            self::STATUS_PROSES     => 'Proses',
            self::STATUS_SELESAI    => 'Selesai',
            self::STATUS_BATAL      => 'Batal',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabel()[$this->status] ?? $this->status;
    }

    public function getTotalKantongAttribute(): int
    {
        // FIX: gunakan relasi 'details' (konsisten)
        return $this->details->sum('jumlah_kantong');
    }

    // ─── Relasi ───────────────────────────────────────────────────────────────

    /**
     * FIX: nama relasi dibuat 'details' (plural) agar konsisten dengan
     * pemanggilan ->load('details'), ->with('details'), $row->details di blade.
     * Nama model juga diperbaiki ke PermintaanDarahPenyimpananDetail.
     */
    public function details(): HasMany
    {
        return $this->hasMany(PermintaanDarahPenyimpananDetail::class, 'permintaan_darah_penyimpanan_id');
    }

    /**
     * Relasi ke user pembuat.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

     public static function generateNomor(): string
{
    $prefix = 'B' . now()->format('ymd');

    do {

        $last = self::withTrashed()
            ->where('no_permintaan', 'like', $prefix . '%')
            ->orderBy('no_permintaan', 'desc')
            ->lockForUpdate()
            ->first();

        $lastNumber = 0;

        if ($last) {
            $lastNumber = (int) substr($last->no_permintaan, -4);
        }

        $nextNumber = $lastNumber + 1;

        $nomor = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

    } while (
        self::withTrashed()
            ->where('no_permintaan', $nomor)
            ->exists()
    );

    return $nomor;
}

    // ─── Scope Filter ─────────────────────────────────────────────────────────

    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['tanggal_minta'])) {
            $query->whereDate('tanggal_minta', $filters['tanggal_minta']);
        }
        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(function ($q) use ($s) {
                $q->where('no_permintaan', 'like', "%{$s}%")
                  ->orWhere('bank_darah_nama', 'like', "%{$s}%");
            });
        }
        return $query;
    }
}