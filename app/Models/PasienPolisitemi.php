<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PasienPolisitemi extends Model
{
    const JENIS_KELAMIN = ['Pria', 'Wanita'];
    const AGAMA = ['Islam', 'Kristen', 'Katholik', 'Hindu', 'Buddha', 'Konghucu'];
    const GOLONGAN_DARAH = ['A', 'B', 'AB', 'O'];
    const RHESUS = ['+', '-'];
    use SoftDeletes;
    protected $table = 'pasien_polisitemi';
    protected $fillable = [
        'kode', 'nama', 'alamat', 'kode_pos', 'tanggal_lahir', 'jenis_kelamin',
        'agama', 'no_telp', 'no_ktp', 'golongan_darah', 'rhesus',
    ];
    protected $casts = ['tanggal_lahir' => 'datetime'];
}

