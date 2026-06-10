<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cabang extends Model
{
    use SoftDeletes;
    const LIST_JENIS = ['UDD', 'Mobil Unit', 'Gudang', 'Lab'];

    protected $table = 'cabang';

    protected $fillable = [
        'kode', 'nama', 'alamat_1', 'alamat_2', 'kode_pos', 'no_telp', 'jenis', 'status',
    ];

    protected $casts = ['status' => 'boolean'];

    public function petugas()
    {
        return $this->hasMany(Petugas::class);
    }

    public function logDonor()
    {
        return $this->hasMany(LogDonor::class);
    }

    public function barang()
    {
        return $this->hasMany(Barang::class);
    }

    public function kantongDarah()
    {
        return $this->hasMany(KantongDarah::class);
    }

    public function pengajuanBarang()
    {
        return $this->hasMany(PengajuanBarang::class);
    }
}
