<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StokDarah extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stok_darah';

    protected $fillable = [
        'no_stok', 'no_kantong', 'jenis_darah', 'golongan_darah',
        'rhesus', 'tgl_aftap', 'tgl_produksi', 'tgl_expired',
        'ruang', 'ml', 'gr', 'skrining', 'no_fpd', 'asal_darah_id',
        'jumlah_masuk', 'jumlah_keluar', 'jumlah_kembali', 'saldo',
        'status_stok', 'penerimaan_id',
    ];

    protected $casts = [
        'tgl_aftap'    => 'date',
        'tgl_produksi' => 'date',
        'tgl_expired'  => 'date',
    ];

    // ─── Relationships ────────────────────────────────────────────────────

    public function penerimaan()
    {
        return $this->belongsTo(PenerimaanProlisPenyimpanan::class, 'penerimaan_id');
    }

    public function transaksi()
    {
        return $this->hasMany(TransaksiStokDarah::class, 'stok_darah_id');
    }

    public function asalDarah()
    {
        return $this->belongsTo(AsalDarah::class, 'asal_darah_id');
    }
    public function pendataanKantong()
   {
       return $this->belongsTo(PendataanKantong::class, 'no_kantong', 'kode');
   }
    // ─── Scopes ───────────────────────────────────────────────────────────

    public function scopeTersedia($query)
    {
        return $query->where('status_stok', 'tersedia')->where('saldo', '>', 0);
    }

    public function scopeByGolongan($query, $gol)
    {
        return $query->where('golongan_darah', $gol);
    }

    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_darah', $jenis);
    }

    public function scopeByRhesus($query, $rhesus)
    {
        return $query->where('rhesus', $rhesus);
    }

    public function scopeByRuang($query, $ruang)
    {
        return $query->where('ruang', $ruang);
    }

}