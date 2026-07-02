<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wilayah extends Model
{
    use SoftDeletes;

    protected $table = 'wilayah';

    protected $fillable = [
        'kode', 'nama',
    ];

    public function kecamatan()
    {
        return $this->hasMany(Kecamatan::class);
    }

    public function donor()
    {
        return $this->hasMany(Donor::class);
    }
}
