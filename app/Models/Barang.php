<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barang extends Model
{
    const JENIS_BARANG = ['Kantong Darah', 'Buku', 'Meja', 'Kursi', 'Kertas', 'Lain Lain'];

    use SoftDeletes;

    protected $table = 'barang';

    protected $fillable = [
        'kode', 'nama', 'satuan', 'stok', 'harga_satuan', 'min_stok', 'jenis_barang', 'cabang_id',
    ];

    public function cabang() { return $this->belongsTo(Cabang::class); }
    public function stok() { return $this->hasMany(Stok::class,  'barang_id', 'id'); }
    public function pengajuanBarang() { return $this->hasMany(PengajuanBarang::class); }
    public function pemakaianBarang() { return $this->hasMany(PemakaianBarang::class); }
    public function pengeluaranBarang() { return $this->hasMany(PengeluaranBarang::class); }
    public function opnameBarang() { return $this->hasMany(OpnameBarang::class); }
    public function purchaseOrder() { return $this->hasMany(PurchaseOrder::class); }
    public function purchaseOrderDetail() { return $this->hasMany(PurchaseOrderDetail::class); }
    public function qcDetailLot() { return $this->hasMany(QcDetailLot::class); }
    public function soItems() { return $this->hasMany(So::class); }
    public function returnSupplierDetail() { return $this->hasMany(ReturnSupplierDetail::class); }
    public function permintaanSupplier() { return $this->hasMany(PermintaanSupplier::class); }
    public function pinjamBarang() { return $this->hasMany(PinjamBarang::class); }
    public function returPinjam() { return $this->hasMany(ReturPinjam::class); }
    public function kelompokBarang() {return $this->belongsTo(KelompokBarang::class);}
    public function getStokAkhirAttribute(){if (!empty($this->attributes['stok'])) { return (int) $this->attributes['stok']; }return $this->stokHistory->sum('qty_in') - $this->stokHistory->sum('qty_out');}

}