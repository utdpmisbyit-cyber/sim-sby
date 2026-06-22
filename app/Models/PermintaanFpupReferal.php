<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermintaanFpupReferal extends Model
{
    use SoftDeletes;

    protected $table = 'permintaan_fpup_referal';

    protected $fillable = [
        'permintaan_fpup_id',
        'fpup_id', // master pasien (tabel fpup)

        // Nomor FPUP & Registrasi
        'no_fpup',
        'no_referal',
        'no_reg',
        'no_reg_online',
        'tgl_minta',
        'jam_minta',
        'tgl_referal',
        'tgl_registrasi_online',

        // Data Rumah Sakit
        'kode_rs',
        'nama_rs',
        'jenis_rs',
        'kategori_rs',
        'bagian',
        'kelas_rawat',

        // Data Pasien
        'nama_pasien',
        'no_ktp',
        'tgl_lahir',
        'umur',
        'kebangsaan',
        'jenis_kelamin',
        'alamat',
        'nama_suami_istri',

        // Khusus Wanita
        'jumlah_kehamilan',
        'abortus',
        'hdn',

        // Data Permintaan
        'jns_permintaan',
        'diagnosa_klinis',
        'hb',
        'alasan_transfusi',
        'transfusi_sebelumnya',
        'transfusi_kapan',
        'reaksi_transfusi',
        'reaksi_gejala',
        'pernah_serologi',
        'serologi_dimana',
        'serologi_hasil',
        'serologi_kapan',

        // Data Darah OS
        'nama_darah_os',
        'gol_rh_os',
        'tgl_terima',
        'jam_terima',
        'pemeriksa',

        // Referal Specific
        'pasien_referal',
        'alasan_referal',
        'alasan_referal_utama',
        'cetak_barcode',

        // Cara Pembayaran & Donor
        'cara_pembayaran',
        'jns_biaya',
        'jns_donor',
        'jml_donor',
        'nama_dokter',
        'nama_os',

        // Status
        'status',
        'status_referal',
    ];

    protected $casts = [
        // Tanggal
        'tgl_minta'               => 'date',
        'tgl_referal'             => 'date',
        'tgl_registrasi_online'   => 'date',
        'tgl_lahir'               => 'date',
        'transfusi_kapan'         => 'date',
        'serologi_kapan'          => 'date',
        'tgl_terima'              => 'date',

        // Boolean
        'hdn'                     => 'boolean',
        'transfusi_sebelumnya'    => 'boolean',
        'reaksi_transfusi'        => 'boolean',
        'pernah_serologi'         => 'boolean',
        'pasien_referal'          => 'boolean',
        'cetak_barcode'           => 'boolean',

        // Integer
        'umur'                    => 'integer',
        'jumlah_kehamilan'        => 'integer',
        'jml_donor'               => 'integer',
    ];

  
    public function permintaanFpup(): BelongsTo
    {
        return $this->belongsTo(PermintaanFpup::class, 'permintaan_fpup_id');
    }

    /**
     * Relasi ke master data pasien (tabel fpup) — sumber autofill modal pasien.
     */
    public function masterPasien(): BelongsTo
    {
        return $this->belongsTo(Fpup::class, 'fpup_id');
    }

    /**
     * Relasi ke detail komponen darah yang diminta.
     */
    public function details(): HasMany
    {
        return $this->hasMany(PermintaanFpupReferalDetail::class, 'permintaan_fpup_referal_id');
    }

  
    public function scopeBaru($query)
    {
        return $query->where('status', 'baru');
    }

    public function scopeProses($query)
    {
        return $query->where('status', 'proses');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    public function scopeStatusReferal($query, string $statusReferal)
    {
        return $query->where('status_referal', $statusReferal);
    }

    public function scopeCito($query)
    {
        return $query->where('jns_permintaan', 'CITO');
    }
    public function crossTestsReferal(): HasMany
    {
        return $this->hasMany(CrossTestReferal::class, 'permintaan_fpup_referal_id');
    }
}