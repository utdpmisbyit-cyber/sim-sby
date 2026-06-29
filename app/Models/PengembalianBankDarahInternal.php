<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengembalianBankDarahInternal extends Model
{
    use SoftDeletes;

    protected $table = 'pengembalian_bank_darah_internal';

    protected $fillable = [
        'no_pengembalian',
        'tgl_pengembalian',
        'bank_darah_asal_id',
        'bank_darah_tujuan_id',
        'petugas_kembali_id',
        'petugas_terima_id',
        'keterangan',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tgl_pengembalian' => 'date',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function details(): HasMany
    {
        return $this->hasMany(PengembalianBankDarahInternalDetail::class, 'pengembalian_id');
    }

    public function bankDarahAsal(): BelongsTo
    {
        return $this->belongsTo(BankDarah::class, 'bank_darah_asal_id');
    }

    public function bankDarahTujuan(): BelongsTo
    {
        return $this->belongsTo(BankDarah::class, 'bank_darah_tujuan_id');
    }

    public function petugasKembali(): BelongsTo
    {
        return $this->belongsTo(Petugas::class, 'petugas_kembali_id');
    }

    public function petugasTerima(): BelongsTo
    {
        return $this->belongsTo(Petugas::class, 'petugas_terima_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Petugas::class, 'created_by');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getTglPengembalianFormattedAttribute(): string
    {
        return $this->tgl_pengembalian
            ? $this->tgl_pengembalian->format('d-m-Y')
            : '';
    }

    public function getJumlahItemAttribute(): int
    {
        return $this->details()->count();
    }
}