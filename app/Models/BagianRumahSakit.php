<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BagianRumahSakit extends Model
{
    use SoftDeletes;
    protected $table = 'bagian_rumah_sakit';
    protected $fillable = ['kode', 'nama'];
}
