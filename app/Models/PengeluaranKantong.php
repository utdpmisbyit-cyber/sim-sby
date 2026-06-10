<?php
// ════════════════════════════════════════════════════════════
// App\Models\PengeluaranKantong
// ════════════════════════════════════════════════════════════
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class PengeluaranKantong extends Model
{
    use SoftDeletes;
 
    protected $table    = 'stok_kantong_keluar';
    protected $fillable = [
        'no_keluar',
        'tgl_keluar',
        'no_kantong',
        'no_lot',
        'merk',
        'jenis',
        'tipe',
        'ukuran',
        'tujuan',
        'keterangan',
        'detail_id',             
        'permintaan_kantong_id', 
        'created_by',
    ];
 
    protected $casts = [
        'tgl_keluar' => 'date',
    ];
 
    // ── Relasi ───────────────────────────────────────────────
 
    /** Stok kantong asal */
    public function stokMasuk()
    {
        return $this->belongsTo(StokKantong::class, 'no_kantong', 'no_kantong');
    }
 
    /** Baris detail permintaan yang dilayani */
    public function detail()
    {
        return $this->belongsTo(PermintaanKantongDetail::class, 'detail_id', 'id');
    }
 
    /** Header permintaan kantong */
    public function permintaan()
    {
        return $this->belongsTo(PermintaanKantong::class, 'permintaan_kantong_id', 'id');
    }
 
    /** User pembuat */
    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'id');
    }
}
 