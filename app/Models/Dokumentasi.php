<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dokumentasi extends Model
{
    protected $table = 'dokumentasi';
    protected $fillable = ['kode', 'uri_text', 'pengajuan_supplier_id'];
    public function pengajuanSupplier() { return $this->belongsTo(PengajuanSupplier::class); }
}
