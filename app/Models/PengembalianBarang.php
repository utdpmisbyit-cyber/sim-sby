<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengembalianBarang extends Model
{
    use SoftDeletes;

    protected $table = 'pengembalian_barang';

    protected $fillable = [
        'no_kembali',
        'tgl_kembali',
        'departemen',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tgl_kembali' => 'date',
    ];

    public function details()
    {
        return $this->hasMany(PengembalianBarangDetail::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Total jumlah barang (semua kondisi) dalam 1 transaksi pengembalian.
     */
    public function getTotalJumlahAttribute(): int
    {
        return (int) $this->details->sum('jumlah');
    }
}