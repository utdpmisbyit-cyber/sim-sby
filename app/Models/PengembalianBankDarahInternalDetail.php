<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengembalianBankDarahInternalDetail extends Model
{
    use HasFactory;

    protected $table = 'pengembalian_bank_darah_internal_detail';

    protected $fillable = [
        'pengembalian_id',
        'no_stok',
        'no_kantong',
        'no_donor',
        'stok_darah_id',
        'jenis_darah',
        'golongan_darah',
        'rhesus',
        'tgl_aftap',
        'tgl_expired',
        'status_stok',
        'status_kembali',
        'alasan_kembali',
        'jumlah',
        'keterangan',
    ];

    protected $casts = [
        'tgl_aftap'   => 'date',
        'tgl_expired' => 'date',
        'jumlah'      => 'integer',
    ];

    /**
     * Header Pengembalian
     */
    public function pengembalian(): BelongsTo
    {
        return $this->belongsTo(
            PengembalianBankDarahInternal::class,
            'pengembalian_id'
        );
    }

    /**
     * Relasi ke stok darah
     */
    public function stokDarah(): BelongsTo
    {
        return $this->belongsTo(
            StokDarah::class,
            'stok_darah_id'
        );
    }
}