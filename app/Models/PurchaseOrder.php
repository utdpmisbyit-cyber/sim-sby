<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;
    protected $table = 'purchase_order';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'string';
    protected $fillable = [
        'no_po', 'tgl_po', 'status_po', 'total_po', 'app_po',
        'supplier_id', 'user_input', 'barang_id', 'anggaran_id',
    ];
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function barang() { return $this->belongsTo(Barang::class); }
    public function anggaran() { return $this->belongsTo(Anggaran::class, 'anggaran_id'); }
    public function details() { return $this->hasMany(PurchaseOrderDetail::class, 'purchase_order_id', 'id'); }
    public function qcBarangMasuk() { return $this->hasMany(QcBarangMasuk::class, 'purchase_order_id', 'no_po'); }
}

