<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PelayananDarah extends Model
{
    use SoftDeletes;

    protected $table = 'pelayanan_darah';

    protected $fillable = [
        'no_pelayanan',
        'pemberian_darah_id',
        'no_pemberian',
        'no_fpup',
        'tgl_fpup',
        'tgl_pelayanan',
        'jam_pelayanan',
        'cara_bayar',
        'jns_biaya',
        'no_register',
        'no_faktur',
        'nama_pasien',
        'nama_dokter',
        'nama_rs',
        'kode_rs',
        'jenis_rs',
        'bagian_rs',
        'kelas_rawat',
        'golongan_darah',
        'rhesus',
        'alamat_os',
        'total_biaya',
        'diskon',
        'total_bayar',
        'terbayar',
        'kembalian',
        'status',
        'petugas_kasir',
        'keterangan',
        'cara_pembayaran',
    ];

    protected $casts = [
        'tgl_fpup'      => 'date',
        'tgl_pelayanan' => 'date',
        'total_biaya'   => 'decimal:2',
        'diskon'        => 'decimal:2',
        'total_bayar'   => 'decimal:2',
        'terbayar'      => 'decimal:2',
        'kembalian'     => 'decimal:2',
    ];

    // Status constants
    const STATUS_BARU      = 'baru';
    const STATUS_PROSES    = 'proses';
    const STATUS_SELESAI   = 'selesai';
    const STATUS_BATAL     = 'batal';

    public static function statusList(): array
    {
        return [
            self::STATUS_BARU    => 'Baru',
            self::STATUS_PROSES  => 'Proses',
            self::STATUS_SELESAI => 'Selesai',
            self::STATUS_BATAL   => 'Batal',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /**
     * Detail baris darah milik pelayanan ini (tabel pelayanan_darah_detail).
     * PERBAIKAN: sebelumnya salah mengarah ke PemberianDarahDetail.
     */
    public function details(): HasMany
    {
        return $this->hasMany(PelayananDarahDetail::class, 'pelayanan_darah_id');
    }

    public function pemberianDarah(): BelongsTo
    {
        return $this->belongsTo(PemberianDarah::class, 'pemberian_darah_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSearch($query, string $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('no_pelayanan', 'like', "%{$keyword}%")
              ->orWhere('no_fpup',     'like', "%{$keyword}%")
              ->orWhere('nama_pasien', 'like', "%{$keyword}%")
              ->orWhere('nama_rs',     'like', "%{$keyword}%")
              ->orWhere('no_register', 'like', "%{$keyword}%");
        });
    }

    public function scopeTanggal($query, ?string $from, ?string $to)
    {
        if ($from) $query->whereDate('tgl_pelayanan', '>=', $from);
        if ($to)   $query->whereDate('tgl_pelayanan', '<=', $to);
        return $query;
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_BARU    => 'badge-info',
            self::STATUS_PROSES  => 'badge-warning',
            self::STATUS_SELESAI => 'badge-success',
            self::STATUS_BATAL   => 'badge-danger',
            default              => 'badge-secondary',
        };
    }

    public function getSisaTagihanAttribute(): float
    {
        return max(0, $this->total_bayar - $this->terbayar);
    }
}