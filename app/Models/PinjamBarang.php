<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PinjamBarang extends Model
{
    use SoftDeletes;
    protected $table = 'pinjam_barang';
    protected $fillable = [
        'kode', 'barang_id', 'petugas_id', 'jumlah_pinjam', 'bagian_id',
        'tanggal_pinjam', 'keterangan', 'diserahkan_ke', 'status',
    ];
    public function barang() { return $this->belongsTo(Barang::class); }
    public function petugas() { return $this->belongsTo(Petugas::class); }
    public function bagian() { return $this->belongsTo(BagianPetugas::class, 'bagian_id'); }
    public function returPinjam() { return $this->hasMany(ReturPinjam::class); }
}
