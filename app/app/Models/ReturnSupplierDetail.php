<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnSupplierDetail extends Model
{
    protected $table = 'return_supplier_detail';
    protected $fillable = ['return_supplier_id', 'barang_id', 'qty_retur', 'harga_retur', 'subtotal_retur'];
    public function returnSupplier() { return $this->belongsTo(ReturnSupplier::class, 'return_supplier_id', 'no_trans_retur'); }
    public function barang() { return $this->belongsTo(Barang::class); }
}
