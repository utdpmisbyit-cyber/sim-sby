<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengeluaranKantongMobileUnit extends Model
{
    use SoftDeletes;

    protected $table = 'pengeluaran_kantong_mobile_unit';

    protected $fillable = [
        'no_keluar',
        'tgl_keluar',
        'no_kantong',
        'no_lot',
        'merk',
        'jenis',
        'tipe',
        'ukuran',
        'jumlah',
        'tujuan',
        'keterangan',
        'mobile_unit_id',
        'asal_darah_id',
        'petugas_id',
        'penerimaan_kantong_id',
        'permintaan_mobile_unit_id',
        'no_permintaan',
        'created_by',
    ];

    protected $casts = [
        'tgl_keluar' => 'date',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function mobilUnit()
    {
        return $this->belongsTo(MobilUnit::class, 'mobile_unit_id');
    }

    public function asalDarah()
    {
        return $this->belongsTo(AsalDarah::class, 'asal_darah_id');
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'petugas_id');
    }

    // ── Auto-generate no_keluar ────────────────────────────────────────────────

    public static function generateNomorKeluar(): string
    {
        $prefix = 'K' . date('Ymd');
        $last   = self::where('no_keluar', 'like', $prefix . '%')
                      ->orderByDesc('no_keluar')
                      ->value('no_keluar');

        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}