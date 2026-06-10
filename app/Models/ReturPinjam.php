<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturPinjam extends Model
{
    use SoftDeletes;
    protected $table = 'retur_pinjam';
    protected $fillable = [
        'kode', 'pinjam_barang_id', 'petugas_id', 'barang_id', 'bagian_petugas_id',
        'jumlah_retur', 'tanggal_retur', 'kondisi_barang', 'return_supplier_id',
    ];
    public function pinjamBarang() { return $this->belongsTo(PinjamBarang::class); }
    public function petugas() { return $this->belongsTo(Petugas::class); }
    public function barang() { return $this->belongsTo(Barang::class); }
    public function bagianPetugas() { return $this->belongsTo(BagianPetugas::class, 'bagian_petugas_id'); }
    public function returnSupplier() { return $this->belongsTo(ReturnSupplier::class, 'return_supplier_id', 'no_trans_retur'); }
}
