<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemberianDarah extends Model
{
    use SoftDeletes;

    protected $table = 'pemberian_darah';

    protected $fillable = [
        'no_pemberian',
        'no_fpup',
        'permintaan_fpup_id',
        'tgl_keluar',
        'jam_keluar',
        'nama_penerima',
        'alamat_penerima',
        'nama_pasien',
        'nama_dokter',
        'nama_rs',
        'kode_rs',
        'jenis_rs',
        'kelas_rawat',
        'gol_rh_pasien',
        'cara_pembayaran',
        'jns_biaya',
        'no_reg_online',
        'tgl_registrasi_online',
        'petugas',
        'kurir_rs',
        'pasien_referal',
        'export_dropping',
        'tgl_export_dropping',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tgl_keluar'            => 'date',
        'tgl_registrasi_online' => 'date',
        'tgl_export_dropping'   => 'datetime',
        'pasien_referal'        => 'boolean',
        'export_dropping'       => 'boolean',
    ];

    // ─── Relationships ──────────────────────────────────────────────────────────

    public function detail(): HasMany
    {
        return $this->hasMany(PemberianDarahDetail::class, 'pemberian_darah_id');
    }

    public function permintaanFpup(): BelongsTo
    {
        return $this->belongsTo(PermintaanFpup::class, 'permintaan_fpup_id');
    }

    // ─── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByTanggal($query, $from, $to = null)
    {
        $query->whereDate('tgl_keluar', '>=', $from);
        if ($to) {
            $query->whereDate('tgl_keluar', '<=', $to);
        }
        return $query;
    }

    // ─── Accessors ──────────────────────────────────────────────────────────────

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'selesai' => 'success',
            'batal'   => 'danger',
            default   => 'warning',
        };
    }
}