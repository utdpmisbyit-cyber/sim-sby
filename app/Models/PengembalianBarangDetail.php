<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengembalianBarangDetail extends Model
{
    protected $table = 'pengembalian_barang_detail';

    protected $fillable = [
        'pengembalian_barang_id',
        'barang_id',
        'no_kantong',
        'jumlah',
        'kondisi',
        'no_trans_stok',
    ];

    public function pengembalian_barang()
    {
        return $this->belongsTo(PengembalianBarang::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    /**
     * Baris stok (ledger qty_in) yang dibuat otomatis untuk detail ini,
     * hanya ada jika kondisi = 'baik'.
     */
    public function stok()
    {
        return $this->belongsTo(Stok::class, 'no_trans_stok', 'no_trans_stok');
    }
}