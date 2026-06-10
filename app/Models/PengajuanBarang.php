<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengajuanBarang extends Model
{
    const JENIS_PENGAJUAN = ['PEMBELIAN', 'PERBAIKAN', 'BARANG_MEDIS', 'NON_MEDIS', 'LAIN_LAIN'];

    use SoftDeletes;
    protected $table = 'pengajuan_barang';
    protected $fillable = [
        'kode', 'tgl_pengajuan', 'jenis_pengajuan', 'status', 'user_input', 'user_proses',
        'cabang_id', 'petugas_id', 'barang_id', 'nama_barang', 'satuan', 'jml_minta',
    ];
    public function cabang() { return $this->belongsTo(Cabang::class); }
    public function petugas() { return $this->belongsTo(Petugas::class); }
    public function barang() { return $this->belongsTo(Barang::class); }
    public function dokumentasiBarang() { return $this->hasOne(DokumentasiBarang::class); }
    public function pemakaianBarang() { return $this->hasMany(PemakaianBarang::class); }
    public function pengeluaranBarang() { return $this->hasMany(PengeluaranBarang::class); }
}
