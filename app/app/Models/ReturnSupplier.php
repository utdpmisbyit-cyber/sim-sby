<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnSupplier extends Model
{
    use SoftDeletes;
    protected $table = 'return_supplier';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'string';
    protected $fillable = [
        'no_trans_retur', 'tgl_retur', 'supplier_id', 'jml_retur',
        'satuan', 'jenis_retur', 'total_retur', 'user_input',
    ];
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function detail() { return $this->hasMany(ReturnSupplierDetail::class, 'return_supplier_id', 'no_trans_retur'); }
    public function returPinjam() { return $this->hasMany(ReturPinjam::class); }
}
