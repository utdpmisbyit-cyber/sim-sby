<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $table = 'supplier';

    protected $fillable = ['kode', 'nama', 'alamat', 'no_telp', 'status'];

    public function pengajuanSupplier() { return $this->hasMany(PengajuanSupplier::class); }
    public function purchaseOrder() { return $this->hasMany(PurchaseOrder::class); }
    public function qcBarangMasuk() { return $this->hasMany(QcBarangMasuk::class); }
    public function returnSupplier() { return $this->hasMany(ReturnSupplier::class); }
    public function permintaanSupplier() { return $this->hasMany(PermintaanSupplier::class); }
}
