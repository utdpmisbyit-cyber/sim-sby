<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PemakaianBarang extends Model
{
    use SoftDeletes;
    protected $table = 'pemakaian_barang';
    protected $fillable = [
        'kode', 'tgl_pemakaian', 'jumlah_pakai', 'keterangan',
        'pengajuan_barang_id', 'barang_id', 'nama_barang', 'user_input',
    ];
    public function pengajuanBarang() { return $this->belongsTo(PengajuanBarang::class); }
    public function barang() { return $this->belongsTo(Barang::class); }
}
