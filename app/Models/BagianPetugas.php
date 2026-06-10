<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BagianPetugas extends Model
{
    use SoftDeletes;

    protected $table = 'bagian_petugas';

    protected $fillable = ['kode', 'nama'];

    public function petugas()
    {
        return $this->hasMany(Petugas::class, 'bagian_id');
    }

    public function pinjamBarang()
    {
        return $this->hasMany(PinjamBarang::class, 'bagian_id');
    }

    public function returPinjam()
    {
        return $this->hasMany(ReturPinjam::class, 'bagian_petugas_id');
    }
}
