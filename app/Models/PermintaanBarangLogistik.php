<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermintaanBarangLogistik extends Model
{
    const STATUS = ['diterima', 'diproses', 'dikirim', 'selesai', 'ditolak'];

    use SoftDeletes;

    protected $table = 'permintaan_barang_logistik';

    protected $fillable = [
        'kode', 'pengajuan_barang_id', 'tgl_terima', 'tgl_proses',
        'jml_acc', 'petugas_gudang_id', 'status', 'catatan', 'user_proses',
    ];

    public function pengajuanBarang() { return $this->belongsTo(PengajuanBarang::class); }
    public function petugasGudang() { return $this->belongsTo(Petugas::class, 'petugas_gudang_id'); }
}