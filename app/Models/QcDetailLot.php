<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QcDetailLot extends Model
{
    protected $table = 'qc_detail_lot';
    protected $fillable = [
        'qc_barang_masuk_id', 'barang_id', 'no_lot', 'jenis_barang',
        'qty_terima', 'harga', 'subtotal_harga', 'tgl_exp_date', 'suhu',
    ];
    public function qcBarangMasuk() { return $this->belongsTo(QcBarangMasuk::class, 'qc_barang_masuk_id','id'); }
    public function barang() { return $this->belongsTo(Barang::class); }
}
