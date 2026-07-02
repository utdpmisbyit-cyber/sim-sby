<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QcBarangMasuk extends Model
{
    use SoftDeletes;
    protected $table = 'qc_barang_masuk';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'string';
    protected $fillable = [
        'no_trans_qc', 'tgl_qc', 'tgl_beli', 'status_qc', 'no_faktur',
        'purchase_order_id', 'supplier_id', 'user_proses',
    ];
    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id'); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function qcDetailLot() { return $this->hasMany(QcDetailLot::class, 'qc_barang_masuk_id', 'id'); }
    public function so() { return $this->hasOne(So::class, 'qc_barang_masuk_id', 'no_trans_qc'); }
}
