<?php 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class PenerimaanKantong extends Model
{
    protected $table = 'penerimaan_kantong';
 
    protected $fillable = [
        'no_transaksi',
        'tanggal',
        'kode_permintaan',
        'no_keluar',
    ];
    
    public $timestamps = true;
    
    public function detail()
    {
        return $this->hasMany(PenerimaanKantongDetail::class, 'penerimaan_id');
    }
}