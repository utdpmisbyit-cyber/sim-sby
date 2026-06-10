<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermintaanSupplier extends Model
{
    use SoftDeletes;
    protected $table = 'permintaan_supplier';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'no_permintaan',
        'tgl_permintaan',
        'supplier_id',
        'barang_id',
        'qty',
        'satuan',
        'status',
        'keterangan',
        'user_input',
    ];
    public function supplier() {
        return $this->belongsTo(Supplier::class,'supplier_id', 'id');
    }

    public function barang() {
        return $this->belongsTo(Barang::class,'barang_id', 'id');
    }
}
