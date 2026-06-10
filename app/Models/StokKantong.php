<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StokKantong extends Model
{
    use SoftDeletes;
    protected $table = 'stok_kantong_masuk';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'no_terima', 'tgl_terima', 'no_kantong',
        'no_lot', 'merk','jenis', 'tipe', 'ukuran','status',
    ];
    public function barang() { return $this->belongsTo(Barang::class); }
    public function pengeluaranKantong() { return $this->hasMany(PengeluaranKantong::class); }
}
