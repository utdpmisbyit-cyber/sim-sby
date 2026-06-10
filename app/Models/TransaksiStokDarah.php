<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransaksiStokDarah extends Model
{
    use HasFactory;

    protected $table = 'transaksi_stok_darah';

    protected $fillable = [
        'no_stok', 'stok_darah_id', 'jenis', 'jumlah',
        'no_referensi', 'sumber', 'referensi_id',
        'keterangan', 'petugas_id', 'created_by',
    ];

    // ─── Relationships ────────────────────────────────────────────────────

    public function stokDarah()
    {
        return $this->belongsTo(StokDarah::class, 'stok_darah_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}