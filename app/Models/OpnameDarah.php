<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpnameDarah extends Model
{
    use SoftDeletes;

    protected $table = 'opname_darah';

    protected $fillable = [
        'no_opname',
        'tgl_opname',
        'lokasi_opname',
        'lokasi_opname_id',
        'status',
        'keterangan',
        'petugas_id',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tgl_opname'  => 'date',
        'approved_at' => 'datetime',
    ];

  
    public function detail(): HasMany
    {
        return $this->hasMany(OpnameDarahDetail::class, 'opname_darah_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Petugas::class, 'petugas_id');
    }
    public function lokasiOpname(): BelongsTo
    {
        return $this->belongsTo(\App\Models\BagianPetugas::class, 'lokasi_opname_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Petugas::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Petugas::class, 'approved_by');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    // ── Accessors ──────────────────────────────────────────────

    public function getJumlahDetailAttribute(): int
    {
        return $this->detail()->count();
    }

    public function getTotalSelisihAttribute(): int
    {
        return $this->detail()->sum('selisih');
    }
}