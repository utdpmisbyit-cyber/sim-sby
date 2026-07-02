<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HakAkses extends Model
{
    use SoftDeletes;

    protected $table = 'hak_akses';

    protected $fillable = ['kode', 'nama', 'description'];

    public function petugas()
    {
        return $this->belongsToMany(Petugas::class, 'hak_akses_petugas', 'hak_akses_id', 'petugas_id');
    }
}
