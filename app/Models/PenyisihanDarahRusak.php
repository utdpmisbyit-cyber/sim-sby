<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenyisihanDarahRusak extends Model
{
    use SoftDeletes;

    protected $table = 'penyisihan_darah_rusak';

    protected $fillable = [
        'no_penyisihan',
        'tgl_penyisihan',
        'alasan',
        'keterangan',
        'status',
        'petugas_id',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tgl_penyisihan' => 'date',
        'approved_at'    => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────────

    public function details(): HasMany
    {
        return $this->hasMany(PenyisihanDarahRusakDetail::class, 'penyisihan_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'petugas_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', 'disetujui');
    }
}