<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisPeriksaSerologi extends Model
{
    use SoftDeletes;

    protected $table = 'jenis_periksa_serologi';

    protected $fillable = ['kode', 'nama'];

    public function serologi()
    {
        return $this->hasMany(Serologi::class, 'jenis_periksa_serologi_id');
    }
}
