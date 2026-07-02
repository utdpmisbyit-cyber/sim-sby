<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengajuanSupplier extends Model
{
    use SoftDeletes;
    protected $table = 'pengajuan_supplier';
    protected $fillable = [
        'kode', 'tgl_pengajuan', 'jenis_pengajuan', 'status', 'tgl_evaluasi',
        'user_input', 'user_proses', 'supplier_id',
    ];
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function dokumentasi() { return $this->hasOne(Dokumentasi::class); }
}
