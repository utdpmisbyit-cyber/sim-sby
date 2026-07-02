<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DokumentasiBarang extends Model
{
    protected $table = 'dokumentasi_barang';
    protected $fillable = ['kode', 'uri_text', 'pengajuan_barang_id'];
    public function pengajuanBarang() { return $this->belongsTo(PengajuanBarang::class); }
}
