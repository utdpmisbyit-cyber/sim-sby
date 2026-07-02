<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class So extends Model
{
    use SoftDeletes;
    protected $table = 'so';
    protected $primaryKey = 'no_trans_so';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['no_trans_so', 'tgl_so', 'barang_id', 'qty_so', 'harga_so', 'qc_barang_masuk_id'];
    public function barang() { return $this->belongsTo(Barang::class); }
    public function qcBarangMasuk() { return $this->belongsTo(QcBarangMasuk::class, 'qc_barang_masuk_id', 'no_trans_qc'); }
}
