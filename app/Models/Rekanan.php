<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rekanan extends Model
{
    use SoftDeletes;
    protected $table = 'rekanan';
    protected $fillable = ['kode', 'nama_rekanan', 'kategori', 'tlp', 'email', 'alamat'];
}
