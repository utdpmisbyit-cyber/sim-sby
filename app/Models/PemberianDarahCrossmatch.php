<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PemberianDarahCrossmatch extends Model
{
    use SoftDeletes;

    
    protected $table = 'pemberian_darah_crossmatch';

    protected $fillable = [
        'no_pemberian',
        'tanggal',
        'jam_keluar',
        'petugas_kode',
        'petugas_nama',
        'nama_penerima',
        'alamat_penerima',
        // Data FPUP
        'no_fpup',
        'tgl_fpup',
        'dokter',
        'kode_rs',
        'nama_rs',
        'pasien',
        'jenis_rs',
        'kelas_rawat',
        'gol_darah_pasien',
        'rh_pasien',
        'kategori',
        'utdd_lain',
        'jns_biaya',
        // Darah yang dikirim
        'jns_darah_kirim',
        'gol_darah_kirim',
        'rh_kirim',
        'jumlah_kantong',
        'dilayani',
        'kurir_rs',
        // Opsi
        'is_kandungan',
        'is_pasien_referral',
        // Registrasi Online
        'no_registrasi_online',
        'tgl_registrasi_online',
        'metode',
        'hasil',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'tanggal'               => 'date',
        'tgl_fpup'              => 'datetime',
        'tgl_registrasi_online' => 'datetime',
        'is_kandungan'          => 'boolean',
        'is_pasien_referral'    => 'boolean',
    ];

   
    public function details(): HasMany
    {
        return $this->hasMany(PemberianDarahCrossmatchDetail::class, 'pemberian_darah_id');
    }

    /* ------------------------------------------------------------------ */
    /*  Business Logic                                                      */
    /* ------------------------------------------------------------------ */

    /**
     * Generate nomor pemberian: D{yy}{mm}{dd}{seq 5-digit}
     * Contoh: D2606140001
     */
    public static function generateNoPemberian(): string
    {
        $prefix = 'D';
        $date   = now()->format('ymd');

        $latest = static::whereDate('created_at', today())
            ->orderByDesc('id')
            ->value('no_pemberian');

        $seq = $latest
            ? ((int) substr($latest, -5)) + 1
            : 1;

        return $prefix . $date . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }

    /* ------------------------------------------------------------------ */
    /*  Accessors                                                          */
    /* ------------------------------------------------------------------ */

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'selesai' => 'success',
            'proses'  => 'warning',
            'batal'   => 'danger',
            default   => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'selesai' => 'Selesai',
            'proses'  => 'Proses',
            'batal'   => 'Batal',
            default   => 'Draft',
        };
    }
}