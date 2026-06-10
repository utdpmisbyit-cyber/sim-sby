<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    protected $table = 'purchase_order_detail';
    protected $fillable = ['purchase_order_id', 'barang_id', 'qty_po', 'harga_po', 'subtotal_po',];
    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id'); }
    public function barang() { return $this->belongsTo(Barang::class); }
}
