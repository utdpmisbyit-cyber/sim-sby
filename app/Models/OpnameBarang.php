<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpnameBarang extends Model
{
    use SoftDeletes;
    protected $table = 'opname_barang';
    protected $primaryKey = 'no_opname';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'no_opname', 'tgl_opname', 'status', 'petugas_id', 'user_input', 'user_proses',
        'barang_id', 'nama_barang', 'qty_sistem', 'qty_fisik', 'selisih',
        'satuan', 'keterangan', 'lokasi',
    ];
    public function petugas() { return $this->belongsTo(Petugas::class); }
    public function barang() { return $this->belongsTo(Barang::class); }
    public function lokasiBagian(){return $this->belongsTo(BagianPetugas::class, 'lokasi', 'id');}
}
