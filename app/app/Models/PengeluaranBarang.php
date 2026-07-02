<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengeluaranBarang extends Model
{
    use SoftDeletes;
    protected $table = 'pengeluaran_barang';
    protected $primaryKey = 'no_trans_keluar';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'no_trans_keluar', 'tgl_keluar', 'user_input', 'user_proses',
        'pengajuan_barang_id', 'barang_id', 'nama_barang', 'no_lot',
        'tgl_expired', 'status', 'qty_keluar', 'satuan', 'keterangan', 'stok_id',
    ];
    public function pengajuanBarang() { return $this->belongsTo(PengajuanBarang::class); }
    public function barang() { return $this->belongsTo(Barang::class); }
    public function stok() { return $this->belongsTo(Stok::class); }
}
