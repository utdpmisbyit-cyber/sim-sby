<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Anggaran extends Model
{
    use SoftDeletes;
    protected $table = 'anggaran';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = ['kode', 'tgl_input', 'tahun_anggaran', 'keterangan', 'nilai_anggaran', 'user_input'];
    protected $casts = ['tgl_input' => 'datetime'];
    public function purchaseOrder() { return $this->hasMany(PurchaseOrder::class); }
    public function petugas(){ return $this->belongsTo(Petugas::class, 'user_input', 'id');}
}
