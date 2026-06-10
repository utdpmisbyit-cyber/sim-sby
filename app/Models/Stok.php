<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stok extends Model
{
    use SoftDeletes;
    protected $table = 'stok';
    protected $primaryKey = 'no_trans_stok';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'no_trans_stok', 'tgl_proses', 'proses', 'barang_id',
        'qty_in', 'qty_out', 'harga', 'keterangan', 'aktif',
    ];
    public function barang() { return $this->belongsTo(Barang::class); }
    public function pengeluaranBarang() { return $this->hasMany(PengeluaranBarang::class); }
}
