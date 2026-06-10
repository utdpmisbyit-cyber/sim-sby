<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengembalianDarahExternal extends Model
{
    use SoftDeletes;

    protected $table = 'pengembalian_darah_external';

    protected $fillable = [
        'no_pengembalian',
        'tgl_pengembalian',
        'tujuan_darah',
        'petugas_terima_id',
        'petugas_kembali_id',
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
        return $this->hasMany(PengembalianDarahExternalDetail::class, 'pengembalian_id');
    }

    public function tujuanDarah()
    {
        return $this->belongsTo(TujuanDarah::class,'tujuan_darah','kode');
    }

    public function petugasTerima()
    {
        return $this->belongsTo(Petugas::class,'petugas_terima','kode');
    }

    public function petugasKembali()
    {
        return $this->belongsTo(Petugas::class,'petugas_kembali','kode');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Petugas::class, 'created_by');
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