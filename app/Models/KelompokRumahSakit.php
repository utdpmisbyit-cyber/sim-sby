<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KelompokRumahSakit extends Model
{
    use SoftDeletes;
    protected $table = 'kelompok_rumah_sakit';
    protected $fillable = ['kode', 'nama'];
    protected $guarded = ['id'];

    public function tujuanDarah() {
        return $this->hasMany(TujuanDarah::class);
    }
}
